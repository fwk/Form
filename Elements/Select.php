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

use Fwk\Form\AbstractElement,
    Fwk\Form\Element;

class Select extends AbstractElement
{
    const ATTR_MULTIPLE = "multiple";

    protected $options = array();

    /**
     * @var boolean
     */
    protected $multiple = false;

    /**
     *
     * @param array $options
     *
     * @return Select
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * @param boolean $bool
     *
     * @return Select
     */
    public function multiple($bool)
    {
        $this->multiple = $bool;

        if($this->multiple === true) {
            $this->setAttr(self::ATTR_MULTIPLE, self::ATTR_MULTIPLE);
        } else {
            $this->removeAttr(self::ATTR_MULTIPLE);
        }

        return $this;
    }

    /**
     *
     * @param string $optKey
     *
     * @return boolean
     */
    public function isOptionSelected($optKey)
    {
        $value = $this->valueOrDefault();
        if (is_array($value) && in_array($optKey, $value)) {
           return true;
        } elseif ($value == $optKey) {
            return true;
        }

        return false;
    }

    /**
     *
     * @return boolean
     */
    public function isMultiple()
    {
        return $this->multiple;
    }
}