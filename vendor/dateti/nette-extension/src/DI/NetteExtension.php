<?php
declare(strict_types=1);

namespace DateTi\DI;

use DateTi\DateTi;
use DateTi\Localization\LocalizationInterface;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use stdClass;

class NetteExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $schema = Expect::structure([
            'timezone' => Expect::string('Europe/Prague'),
            'localizations' => Expect::array()
        ]);

        $builder = $this->getContainerBuilder();
        $configData = $this->getConfig();
        $processor = new Processor();

        $config = $processor->process($schema, $configData);

        $this->checkConfig($config);

        foreach ($config->localizations as $index => $localization) {
            $builder->addDefinition($this->prefix('localization.' . $index))
                ->setFactory($localization);
        }

        $timezone = new \DateTimeZone($config->timezone);
        $builder->addDefinition($this->prefix('dateti'))
            ->setFactory(DateTi::class, ['now', $timezone]);

    }

    public function beforeCompile()
    {

    }

    protected $defaultWork = [
        'start' => [
            'hour' => 8,
            'minute' => 0,
        ],
        'end' => [
            'hour' => 16,
            'minute' => 30,
        ],
        'weekend' => false,
    ];

    protected $defaultShipper = [
        'endHour' => 14,
        'endMinute' => 0,
        'weekend' => false,
        'deliveryTime' => 1,
    ];

    protected function checkConfig(stdClass & $config): void
    {
        $this->checkLocalizations($config->localizations);
        //$this->checkCountry($config->country);
    }

    protected function checkLocalizations(array $localizations)
    {
        foreach ($localizations as $localization) {
            if (!class_exists($localization)) {
                throw new \RuntimeException('Localization ' . $localization . ' does not exists.');
            }
        }
    }

    protected function checkCountry(array & $config): void
    {
        foreach ($config as $group => $setting) {
            if (!array_key_exists('country', $setting)) {
                $config[$group]['country'] = 'CzechRepublic';
            }
        }
    }
}
