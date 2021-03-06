<?php

/**
 * PHP Service Bus demo application
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */
declare(strict_types = 1);

namespace App\Vehicle\Manage\Add;

use Amp\Promise;
use App\Vehicle\Brand\VehicleBrandFinder;
use App\Vehicle\Manage\Add\Contract\AddVehicle;
use App\Vehicle\Manage\Add\Contract\AddVehicleValidationFailed;
use App\Vehicle\Vehicle;
use App\Vehicle\VehicleId;
use ServiceBus\Common\Context\ServiceBusContext;
use ServiceBus\EventSourcing\EventSourcingProvider;
use ServiceBus\EventSourcing\Indexes\IndexKey;
use ServiceBus\EventSourcing\Indexes\IndexValue;
use ServiceBus\EventSourcing\IndexProvider;
use ServiceBus\Services\Annotations\CommandHandler;
use function Amp\call;

/**
 * Add new vehicle
 */
final class HandleAddVehicle
{
    /**
     * @CommandHandler(
     *     description="Add new vehicle",
     *     validate=true,
     *     defaultValidationFailedEvent="App\Vehicle\Manage\Add\Contract\AddVehicleValidationFailed",
     *     defaultThrowableEvent="App\Vehicle\Manage\Add\Contract\AddVehicleFailed"
     * )
     */
    public function handle(
        AddVehicle $command,
        ServiceBusContext $context,
        VehicleBrandFinder $vehicleBrandFinder,
        IndexProvider $indexProvider,
        EventSourcingProvider $eventSourcingProvider
    ): Promise {
        return call(
            static function () use ($command, $context, $vehicleBrandFinder, $indexProvider, $eventSourcingProvider): \Generator
            {
                /** @var \App\Vehicle\Brand\VehicleBrand|null $brand */
                $brand = yield $vehicleBrandFinder->findOneByTitle($command->brand);

                if ($brand === null)
                {
                    return $context->delivery(AddVehicleValidationFailed::invalidBrand($context->traceId()));
                }

                $vehicle = Vehicle::create(
                    $brand,
                    $command->model,
                    $command->year,
                    $command->registrationNumber,
                    $command->color
                );

                $indexKey = new IndexKey('vehicle', $command->registrationNumber);

                /** @var IndexValue|null $storedValue */
                $storedValue = yield $indexProvider->get($indexKey);

                /** Vehicle doesn`t exist  */
                if ($storedValue === null)
                {
                    yield $indexProvider->add($indexKey, new IndexValue($vehicle->id()->toString()));

                    return yield $eventSourcingProvider->save($vehicle, $context);
                }

                return yield $context->delivery(
                    AddVehicleValidationFailed::duplicateStateRegistrationNumber(
                        $context->traceId(),
                        new VehicleId((string) $storedValue->value)
                    )
                );
            }
        );
    }
}
