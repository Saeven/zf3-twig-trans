<?php

/**
 * This file is part of the Twig Gettext utility.
 * Adapted for use within CirclicalTwigTrans
 *
 *  (c) Saša Stamenković <umpirsky@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CirclicalTwigTrans\Test\Loader;

/**
 * Loads template from the filesystem.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Filesystem extends \Twig\Loader\FilesystemLoader
{
    /**
     * Hacked find template to allow loading templates by absolute path.
     *
     * @param string $name template name or absolute path
     * @param null   $throw
     *
     * @return bool|string
     * @throws \Twig_Error_Loader
     */
    protected function findTemplate($name, $throw = null)
    {
        $result = parent::findTemplate($name, false);
        if ($result === false) {
            return __DIR__ . '/../Test/Fixtures/twig/empty.twig';
        }

        return $result;
    }
}