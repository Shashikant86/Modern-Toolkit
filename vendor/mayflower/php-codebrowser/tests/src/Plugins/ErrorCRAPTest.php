<?php
/**
 * Test case
 *
 * Copyright (c) 2007-2010, Mayflower GmbH
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Mayflower GmbH nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage PHPUnit
 * @author     Simon Kohlmeyer <simon.kohlmeyer@mayflower.de
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.phpunit.de/
 * @since      File available since  0.9.0
 */

require_once realpath(dirname(__FILE__) . '/../../AbstractTests.php');
require_once realpath(dirname(__FILE__) . '/../../../src/IssueXml.php');

/**
 * CbErrorCRAPTest
 *
 * @category   PHP_CodeBrowser
 * @package    PHP_CodeBrowser
 * @subpackage PHPUnit
 * @author     Simon Kohlmeyer <simon.kohlmeyer@mayflower.de>
 * @copyright  2007-2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since  0.9.0
 */
class CbErrorCRAPTest extends CbAbstractTests
{
    /**
     * The object to test.
     *
     * @var CbErrorCRAP
     */
    protected $_cbErrorCrap;

    /**
     * The xml string to test the plugin against.
     *
     * @var String
     */
    protected $_testXml = <<<HERE
<?xml version="1.0" encoding="UTF-8"?>
<coverage generated="1279366386">
  <project timestamp="1279366386">
    <file name="/test/file">
      <class name="Class"
             namespace="global"
             fullPackage="Full_Package"
             category="Category"
             package="Package">
      </class>
      <line num="50" type="stmt" count="1"/>
      <line num="143" type="method" name="method_1" crap="1" count="3"/>
      <line num="144" type="stmt" count="3"/>
      <line num="145" type="stmt" count="3"/>
      <line num="162" type="method" name="method_2" crap="100" count="3"/>
      <line num="164" type="stmt" count="3"/>
      <line num="165" type="stmt" count="3"/>
    </file>
    <file name="/has/no/crap">
      <class name="NoCrapClass"
             namespace="global"
             fullPackage="Full_Package"
             category="Category"
             package="Package">
      </class>
      <line num="50" type="stmt" count="1"/>
      <line num="143" type="method" name="method_1" count="3"/>
      <line num="144" type="stmt" count="3"/>
      <line num="145" type="stmt" count="3"/>
      <line num="162" type="method" name="method_2" count="3"/>
      <line num="164" type="stmt" count="3"/>
      <line num="165" type="stmt" count="3"/>
    </file>
  </project>
</coverage>
HERE;

    /**
     * (non-PHPdoc)
     * @see tests/cbAbstractTests#setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $issueXML = new CbIssueXML();
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->loadXML($this->_testXml);
        $issueXML->addXMLFile($xml);
        $this->_cbErrorCrap = new CbErrorCRAP($issueXML);
    }

    /**
     * Test getFilelist
     *
     * @return  void
     */
    public function test__getFilelist()
    {
        $expected = array(
            new CbFile(
                '/test/file',
                array(
                    new CbIssue(
                        '/test/file',
                        143,
                        143,
                        'CRAP',
                        '1',
                        'Notice'
                    ),
                    new CbIssue(
                        '/test/file',
                        162,
                        162,
                        'CRAP',
                        '100',
                        'Error'
                    )
                )
            ),
            new CbFile(
                '/has/no/crap',
                array()
            )
        );
        $actual = $this->_cbErrorCrap->getFilelist();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test getFilelist with limit set
     *
     * @return  void
     */
    public function test__getFilelistWithLimit()
    {
        $issueXML = new CbIssueXML();
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->loadXML($this->_testXml);
        $issueXML->addXMLFile($xml);
        $this->_cbErrorCrap = new CbErrorCRAP(
            $issueXML,
            array('threshold' => 30)
        );

        $expected = array(
            new CbFile(
                '/test/file',
                array(
                    new CbIssue(
                        '/test/file',
                        162,
                        162,
                        'CRAP',
                        '100',
                        'Error'
                    )
                )
            ),
            new CbFile(
                '/has/no/crap',
                array()
            )
        );
        $actual = $this->_cbErrorCrap->getFilelist();
        $this->assertEquals($expected, $actual);
    }

}
