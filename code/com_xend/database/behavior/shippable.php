<?php
/**
 * Nucleon Plus - Xend
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComXendDatabaseBehaviorShippable extends KDatabaseBehaviorAbstract
{
    public function getShippingCost($destination, $weight)
    {
        return (float) $this->getObject('com:xend.model.shippingrates')
            ->getRate($destination, $weight)
        ;
    }
}
