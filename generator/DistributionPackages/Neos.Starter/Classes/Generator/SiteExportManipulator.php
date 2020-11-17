<?php


namespace Neos\Starter\Generator;


class SiteExportManipulator
{
    private GenerationContextInterface $generator;
    private Result $result;
    private string $fileName;
    private string $initialVariantName;
    private string $renamedVariantName;

    private ?string $siteXml = null;

    public function __construct(GenerationContextInterface $generator, Result $result, string $fileName)
    {
        $this->generator = $generator;
        $this->result = $result;
        $this->fileName = $fileName;
    }

    public function removeProperties(string ...$propertiesToRemove): void
    {

    }

    public function setInitialSiteXml(string $siteXml)
    {
        $this->siteXml = $siteXml;
    }

    public function onlyKeepSingleLanguageVariantAndRenameTo(string $initialVariantName, string $renamedVariantName)
    {
        $this->initialVariantName = $initialVariantName;
        $this->renamedVariantName = $renamedVariantName;
    }

    public function generate()
    {
        if ($this->siteXml) {
            $siteXml = $this->siteXml;
            $xml = new \DOMDocument();
            $xml->loadXML($siteXml);
            $xpath = new \DOMXPath($xml);
            $siteNode = $xpath->evaluate('site')->item(0);
            assert($siteNode instanceof \DOMElement, get_class($siteNode));
            $siteNodeName = 'site';
            $siteNode->setAttribute('siteNodeName', $siteNodeName);
            // TODO - hardcoded "neosdemo"
            $rootNode = $xpath->evaluate('site/nodes/node[@nodeName = "neosdemo"]')->item(0);
            assert($rootNode instanceof \DOMElement, get_class($rootNode));
            $rootNode->setAttribute('nodeName', $siteNodeName);

            $siteNode->setAttribute('siteResourcesPackageKey', $this->generator->getConfiguration()->getSitePackageKey());
            $siteNode->setAttribute('name', $this->generator->getConfiguration()->getSitePackageKey() . ' Site');


            $variants = $xpath->evaluate('//variant[@nodeType]');
            assert($variants instanceof \DOMNodeList);
            foreach ($variants as $v) {
                assert($v instanceof \DOMElement, get_class($v));

                $nodeType = $v->getAttribute('nodeType');
                $nodeType = str_replace('Neos.Demo', $this->generator->getConfiguration()->getSitePackageKey(), $nodeType);
                $v->setAttribute('nodeType', $nodeType);
            }



            if ($this->initialVariantName) {
                $res = $xpath->evaluate('//node/variant[./dimensions/language/text() != "' . $this->initialVariantName . '"]');
                assert($res instanceof \DOMNodeList, get_class($res));
                foreach ($res as $r) {
                    assert($r instanceof \DOMElement, get_class($r));
                    $r->parentNode->removeChild($r);
                }

                $res = $xpath->evaluate('//node/variant/dimensions/language[./text() = "' . $this->initialVariantName . '"]');
                assert($res instanceof \DOMNodeList, get_class($res));
                foreach ($res as $r) {
                    assert($r instanceof \DOMElement, get_class($r));
                    if (empty($this->renamedVariantName)) {
                        $r->parentNode->removeChild($r);
                    } else {
                        $r->textContent = $this->renamedVariantName;
                    }
                }
            }

            $this->result->addStringFile($this->fileName, StringBuilder::fromString($xml->saveXML()));
        }
    }
}
