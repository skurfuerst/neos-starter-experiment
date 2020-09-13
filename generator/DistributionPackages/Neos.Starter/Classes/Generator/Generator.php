<?php


namespace Neos\Starter\Generator;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\Dto\Profile;
use Neos\Starter\Generator\Hooks\JsonFileManipulator;
use Neos\Starter\Generator\Hooks\StringFileManipulator;
use Neos\Starter\Generator\Hooks\YamlFileManipulator;
use Neos\Starter\Utility\YamlWithComments;

class Generator implements GenerationContextInterface
{

    protected Configuration $configuration;

    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;


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

        $featureClassNames = self::featureImplementations($this->objectManager);
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

    /**
     * @Flow\CompileStatic
     * @param ObjectManagerInterface $objectManager
     */
    protected static function featureImplementations($objectManager)
    {
        $reflectionService = $objectManager->get(ReflectionService::class);
        return $reflectionService->getAllImplementationClassNamesForInterface(FeatureInterface::class);
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
