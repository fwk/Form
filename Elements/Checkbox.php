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

class Checkbox extends AbstractElement
{
     const ATTR_CHECKED = "checked";

     public function setDefault($value)
     {
        if($value == 'on' || $value == 'checked' || $value) {
            $value = true;
            $this->setAttr(self::ATTR_CHECKED, self::ATTR_CHECKED);
        } else {
            $value = false;
            $this->removeAttr(self::ATTR_CHECKED);
        }

        parent::setDefault($value);
    }

    public function setValue($value)
    {
        if($value == 'on' || $value == 'checked' || $value) {
            $value = true;
            $this->setAttr(self::ATTR_CHECKED, self::ATTR_CHECKED);
        } else {
            $value = false;
            $this->removeAttr(self::ATTR_CHECKED);
        }

        parent::setValue($value);
    }
}