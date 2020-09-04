<?php

namespace Neos\Starter\Tests\Unit;

/*
 * This file is part of the Neos.Flow package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Tests\UnitTestCase;
use Neos\Flow\Aop;
use Neos\Starter\Utility\YamlWithComments;
use Symfony\Component\Yaml\Yaml;

class YamlWithCommentsTest extends UnitTestCase
{

    /**
     * @test
     */
    public function yamlGenerationWithCommentsAndStringKeys()
    {
        $x = [
            'Neos.Neos:Foo' => [
                'myValue' => 'foo',
                'isAbstract' => true,
                'isAbstract##' => YamlWithComments::comment( 'foo'),
                'myValue2' => 'bla',
            ],
            'Neos.Neos:Foo##' => YamlWithComments::comment("My commentMy commentMy\ncommentMy commentMy commentMy commentMy commentMy commentMy commentMy\ncommentMy commentMy commentMy commentMy commentMy commentMy commentMy commentMy commentMy comment"),
        ];

        $expected = <<<EOF
# My commentMy commentMy
# commentMy commentMy commentMy commentMy commentMy commentMy commentMy
# commentMy commentMy commentMy commentMy commentMy commentMy commentMy commentMy commentMy comment
'Neos.Neos:Foo':
    myValue: foo

    # foo
    isAbstract: true
    myValue2: bla
EOF;

        $this->assertEquals(trim($expected), trim(YamlWithComments::dump($x)));
    }

    /**
     * @test
     */
    public function yamlGenerationWithCommentsAndNumberKeys()
    {
        $x = [
            'Neos.Neos:Foo' => [
                'volumes' => [
                    'a',
                    'b',
                    YamlWithComments::comment('Mein Kommentar'),
                    'c'
                ],
            ],
        ];

        $expected = <<<EOF
'Neos.Neos:Foo':
    volumes:
        - a
        - b

        # Mein Kommentar
        - c
EOF;

        $this->assertEquals(trim($expected), trim(YamlWithComments::dump($x)));
    }
}
