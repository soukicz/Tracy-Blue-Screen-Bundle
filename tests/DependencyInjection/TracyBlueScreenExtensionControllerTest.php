<?php

declare(strict_types = 1);

namespace VasekPurchart\TracyBlueScreenBundle\DependencyInjection;

use VasekPurchart\TracyBlueScreenBundle\BlueScreen\ControllerBlueScreenExceptionListener;

class TracyBlueScreenExtensionControllerTest extends \Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase
{

	public function setUp(): void
	{
		parent::setUp();
		$this->setParameter('kernel.root_dir', __DIR__);
		$this->setParameter('kernel.logs_dir', __DIR__);
		$this->setParameter('kernel.cache_dir', __DIR__ . '/tests-cache-dir');
		$this->setParameter('kernel.environment', 'dev');
		$this->setParameter('kernel.debug', true);
	}

	/**
	 * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
	 */
	protected function getContainerExtensions(): array
	{
		return [
			new TracyBlueScreenExtension(),
		];
	}

	public function testEnabledByDefault(): void
	{
		$this->load();

		$this->assertContainerBuilderHasService('vasek_purchart.tracy_blue_screen.blue_screen.controller_blue_screen_exception_listener', ControllerBlueScreenExceptionListener::class);
		$this->assertContainerBuilderHasServiceDefinitionWithTag('vasek_purchart.tracy_blue_screen.blue_screen.controller_blue_screen_exception_listener', 'kernel.event_listener', [
			'event' => 'kernel.exception',
			'priority' => '%' . TracyBlueScreenExtension::CONTAINER_PARAMETER_CONTROLLER_LISTENER_PRIORITY . '%',
		]);

		$this->compile();
	}

	public function testDisabled(): void
	{
		$this->load([
			'controller' => [
				'enabled' => false,
			],
		]);

		$this->assertContainerBuilderNotHasService('vasek_purchart.tracy_blue_screen.blue_screen.controller_blue_screen_exception_listener');

		$this->compile();
	}

	public function testEnabled(): void
	{
		$this->load([
			'controller' => [
				'enabled' => true,
			],
		]);

		$this->assertContainerBuilderHasService('vasek_purchart.tracy_blue_screen.blue_screen.controller_blue_screen_exception_listener', ControllerBlueScreenExceptionListener::class);
		$this->assertContainerBuilderHasServiceDefinitionWithTag('vasek_purchart.tracy_blue_screen.blue_screen.controller_blue_screen_exception_listener', 'kernel.event_listener', [
			'event' => 'kernel.exception',
			'priority' => '%' . TracyBlueScreenExtension::CONTAINER_PARAMETER_CONTROLLER_LISTENER_PRIORITY . '%',
		]);

		$this->compile();
	}

	public function testConfigureListenerPriority(): void
	{
		$this->load([
			'controller' => [
				'listener_priority' => 123,
			],
		]);

		$this->assertContainerBuilderHasParameter(TracyBlueScreenExtension::CONTAINER_PARAMETER_CONTROLLER_LISTENER_PRIORITY, 123);

		$this->compile();
	}

}
