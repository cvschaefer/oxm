<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\OXM\Tools\Console\Command\ClearCache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to clear the metadata cache of the various cache drivers.
 *
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 * @author      Igor Golovanov <igor.golovanov@gmail.com>
 */
class MetadataCommand extends Command
{
    /**
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('oxm:clear-cache:metadata')
        ->setDescription('Clear all metadata cache of the various cache drivers.')
        ->setDefinition(array())
        ->setHelp(<<<EOT
Clear all metadata cache of the various cache drivers.
EOT
        );
    }

    /**
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $xem = $this->getHelper('xem')->getXmlEntityManager();
        $cacheDriver = $xem->getConfiguration()->getMetadataCacheImpl();

        if ( ! $cacheDriver) {
            throw new \InvalidArgumentException('No Metadata cache driver is configured on given XmlEntityManager.');
        }

        if ($cacheDriver instanceof \Doctrine\Common\Cache\ApcCache) {
            throw new \LogicException("Cannot clear APC Cache from Console, its shared in the Webserver memory and not accessible from the CLI.");
        }

        $output->write('Clearing ALL Metadata cache entries' . PHP_EOL);

        $cacheIds = $cacheDriver->deleteAll();

        if ($cacheIds) {
            foreach ($cacheIds as $cacheId) {
                $output->write(' - ' . $cacheId . PHP_EOL);
            }
        } else {
            $output->write('No entries to be deleted.' . PHP_EOL);
        }
    }
}
