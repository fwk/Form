<?php
/**
 * Fwk
 *
 * Copyright (c) 2010-2011, Julien Ballestracci <julien@nitronet.org>.
 * All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Fwk
 * @subpackage Form
 * @subpackage Elements
 * @author     Julien Ballestracci <julien@nitronet.org>
 * @copyright  2011-2012 Julien Ballestracci <julien@nitronet.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpfwk.com
 */
namespace Fwk\Form\Elements;

use Fwk\Form\AbstractElement;
use Fwk\Form\Element;

class Group extends AbstractElement
{
    /**
     * @var array
     */
    protected $elements = array();

    /**
     * @var string
     */
    protected $pattern;

    /**
     *
     * @param Element $element
     *
     * @return Form
     */
    public function add(Element $element)
    {
        $this->elements[] = $element;

        return $this;
    }

    /**
     *
     * @param array $elements
     *
     * @return Form
     */
    public function addAll(array $elements)
    {
        foreach ($elements as $element) {
            $this->add($element);
        }

        return $this;
    }

    /**
     *
     * @param string $elementName
     *
     * @return boolean
     */
    public function has($elementName)
    {
        foreach ($this->elements as $element) {
            if($elem instanceof Group && $elem->has($elementName)) {
                return true;
            } elseif($element->getName() == $elementName) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param string $name
     *
     * @throws \RuntimeException if element is unknown
     * @return Element
     */
    public function element($elementName)
    {
        foreach ($this->elements as $element) {
            if($element->getName() == $elementName) {
                return $element;
            } elseif($element instanceof Group) {
                try {
                    return $element->element($name);
                } catch(\RuntimeException $e) {
                }
            }
        }

        throw new \RuntimeException(
            sprintf("Unknown Group element '%s' (group: %s)", $elementName, $this->getName())
        );
    }

    /**
     * @param string $elementName
     *
     * @return Form
     */
    public function remove($elementName)
    {
        $elem = $this->element($elementName);
        $final = array();

        foreach ($this->elements as $element) {
            if ($element !== $elem) {
                $final[] = $element;
            }
        }
        $this->elements = $final;

        return $this;
    }

    /**
     * @return array
     */
    public function elements()
    {
        return $this->elements;
    }

    /**
     * @return string
     */
    public function getFirstId()
    {
        $elements = $this->elements;
        $first = array_shift($elements);

        if(!$first instanceof Element) {
            $id = rand(0,9999);
        } else {
            $id = $first->attribute(Element::ATTR_ID, rand(0,9999));
        }

        return $id;
    }

    /**
     *
     * @return string
     *
     * @throws \LogicException
     */
    public function getValue() {
        if (empty($this->pattern)) {
                throw new \LogicException('Group pattern is not defined.');
        }

        $patts = $vals = array();
        foreach($this->elements as $element) {
            $patts[]    = ':'. $element->getName();
            $vals[]     = $element->valueOrDefault();
        }

        return str_replace($patts, $vals, $this->pattern);
    }

    /**
     * @return string
     */
    public function valueOrDefault()
    {
        if(!empty($this->pattern)) {
            return $this->getValue();
        }

        return $this->default;
    }

    /**
     *
     * @param type $pattern
     *
     * @return Group
     */
    public function pattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }
}