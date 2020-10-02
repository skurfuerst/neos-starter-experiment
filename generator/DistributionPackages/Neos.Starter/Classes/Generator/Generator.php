<?php


namespace Neos\Starter\Generator;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\Dto\Profile;
use Neos\Utility\PositionalArraySorter;

class Generator implements GenerationContextInterface
{

    protected Configuration $configuration;

    /**
     * @Flow\InjectConfiguration(path="features")
     * @var array
     */
    protected $registeredFeatures;


    protected $activeFeatures;

    protected $inactiveFeatures;

    /**
     * Generator constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function generate(): Result
    {
        $distributionBuilder = new DistributionBuilder($this);

        $featureClassNames = $this->getFeatureImplementations();
        $activeFeatures = [];
        $inactiveFeatures = [];
        foreach ($featureClassNames as $featureClassName) {
            if ($this->configuration->getFeatures()->isEnabled($featureClassName)) {
                $activeFeatures[$featureClassName] = $featureClassName::create($this, $distributionBuilder);
            } else {
                $inactiveFeatures[$featureClassName] = $featureClassName::create($this, $distributionBuilder);
            }
        }
        $this->activeFeatures = $activeFeatures;
        $this->inactiveFeatures = $inactiveFeatures;


        foreach ($this->activeFeatures as $instanciatedFeature) {
            assert($instanciatedFeature instanceof FeatureInterface);
            $instanciatedFeature->registerHooksBeforeActivation();
        }

        foreach ($this->activeFeatures as $instanciatedFeature) {
            assert($instanciatedFeature instanceof FeatureInterface);
            $instanciatedFeature->activate();
        }

        foreach ($this->inactiveFeatures as $instanciatedFeature) {
            assert($instanciatedFeature instanceof FeatureInterface);
            $instanciatedFeature->deactivate();
        }

        return $distributionBuilder->generate();
    }

    /**
     * @return Configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    protected function getFeatureImplementations()
    {
        return (new PositionalArraySorter($this->registeredFeatures))->getSortedKeys();
    }

    public function getCurrentlyActiveProfile(): Profile
    {
        return Profile::fromComposerJsonString(file_get_contents('../profiles/' . $this->configuration->getProfileName()->jsonSerialize() . '/composer.json'));
    }

    public function isFeatureEnabled(string $class): bool
    {
        return isset($this->activeFeatures[$class]);
    }
}
