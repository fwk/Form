<?php
/**
 * Fwk
 *
 * Copyright (c) 2011-2012, Julien Ballestracci <julien@nitronet.org>.
 * All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
 * PHP Version 5.3
 *
 * @category  Form
 * @package   Fwk\Form
 * @author    Julien Ballestracci <julien@nitronet.org>
 * @copyright 2011-2012 Julien Ballestracci <julien@nitronet.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://www.phpfwk.com
 */
namespace Fwk\Form;

use Fwk\Form\Form;

interface Element
{
    const ATTR_VALUE            = "value";
    const ATTR_TYPE             = "type";
    const ATTR_NAME             = "name";
    const ATTR_ID               = "id";
    const ATTR_REQUIRED         = "required";
    const ATTR_READONLY         = "readonly";
    const ATTR_PLACEHOLDER      = "placeholder";

    const ATTR_LABEL_POSITION   = "_labelPosition";

    const LABEL_POSITION_LEFT   = "left";
    const LABEL_POSITION_RIGHT  = "right";

    public function getName();

    public function setParent(Form $form);

    public function getParent();

    public function setAttr($attrName, $attrValue);

    public function attribute($attrName, $default = null);

    public function removeAttr($attrName);

    public function value();

    public function setValue($value);

    public function valueOrDefault();

    public function validate();

    public function getError();

    public function hasError();

    public function clear();

    public function setDefault($defaultValue);

    public function getDefault();

    public function attributes();

    public function hasValue();

    public function hasDefault();

    public function label($label);

    public function hint($hint);

    public function getLabel();

    public function getHint();

    public function hasLabel();

    public function hasHint();

    public function filter(Filter $filter, $errorMessage = null);

    public function sanitizer(Sanitizer $sanitizer);
}