<?php

namespace Ubermanu\StaticCms\Console\Command;

use Magento\Cms\Model\Block;
use Magento\Cms\Model\Page;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ubermanu\StaticCms\Model\BlockRepository;
use Ubermanu\StaticCms\Model\PageRepository;
use Ubermanu\StaticCms\Parser\BlockParser;
use Ubermanu\StaticCms\Parser\PageParser;

class ImportCommand extends Command
{
    /**
     * @var State
     */
    protected $state;

    /**
     * @var BlockParser
     */
    protected $blockParser;

    /**
     * @var PageParser
     */
    protected $pageParser;

    /**
     * @var BlockRepository
     */
    protected $blockRepository;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    public function __construct(
        State $state,
        BlockParser $blockParser,
        PageParser $pageParser,
        BlockRepository $blockRepository,
        PageRepository $pageRepository,
        string $name = null
    ) {
        $this->state = $state;
        $this->blockParser = $blockParser;
        $this->pageParser = $pageParser;
        $this->blockRepository = $blockRepository;
        $this->pageRepository = $pageRepository;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('cms:static:import');
        $this->setDescription('Import static CMS blocks and pages into the store');

        $this->addArgument('file', InputArgument::REQUIRED, 'File to import');

        // TODO: Add support for widgets
        $this->addOption(
            'type',
            't',
            InputOption::VALUE_REQUIRED,
            'Type of content (block|page)',
        );

        $this->addOption(
            'store',
            's',
            InputOption::VALUE_OPTIONAL,
            'Store ID',
            Store::DEFAULT_STORE_ID
        );
    }

    /**
     * TODO: Use a more common approach so it can work with any model
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        $filename = $input->getArgument('file');

        if (!file_exists($filename)) {
            $output->writeln('<error>File does not exist</error>');
            return 1;
        }

        if (!in_array($input->getOption('type'), ['block', 'page'])) {
            $output->writeln('<error>Invalid type</error>');
            return 1;
        }

        $defaultIdentifier = $this->getIdentifier($filename);

        if ($input->getOption('type') === 'block') {

            /** @var Block $block */
            $block = $this->blockParser->parseFile($filename);
            if (!$block->getIdentifier()) {
                $block->setIdentifier($defaultIdentifier);
                $output->writeln('<comment>No identifier found, using filename: ' . $defaultIdentifier . '</comment>');
            }

            // Update the original block with the new data
            // TODO: Move this into the repository?
            try {
                $original = $this->blockRepository->getByIdentifier($block->getIdentifier());
                $block = $original->addData($block->getData());
            } catch (NoSuchEntityException $e) {
                $block->isObjectNew(true);
            }

            // Set the store id
            // TODO: Avoid updating that if not passed in args and already exists
            $block->setStoreId($input->getOption('store'));

            try {
                $this->blockRepository->save($block);
                $output->writeln(sprintf('<info>✓ Block %s %s</info>', $block->getIdentifier(), $block->isObjectNew() ? 'created' : 'updated'));
            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                return 1;
            }
        }

        if ($input->getOption('type') === 'page') {

            /** @var Page $page */
            $page = $this->pageParser->parseFile($filename);
            if (!$page->getIdentifier()) {
                $page->setIdentifier($defaultIdentifier);
                $output->writeln('<comment>No identifier found, using filename: ' . $defaultIdentifier . '</comment>');
            }

            // Update the original page with the new data
            // TODO: Move this into the repository?
            try {
                $original = $this->pageRepository->getByIdentifier($page->getIdentifier());
                $page = $original->addData($page->getData());
            } catch (NoSuchEntityException $e) {
                $page->isObjectNew(true);
            }

            // Set the store id
            // TODO: Avoid updating that if not passed in args and already exists
            $page->setStoreId($input->getOption('store'));

            try {
                $this->pageRepository->save($page);
                $output->writeln(sprintf('<info>✓ Page %s %s</info>', $page->getIdentifier(), $page->isObjectNew() ? 'created' : 'updated'));
            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                return 1;
            }
        }

        return 0;
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getIdentifier(string $filename): string
    {
        return explode('.', basename($filename))[0];
    }
}
