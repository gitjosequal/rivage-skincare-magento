<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Console;

use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Setup\Fixtures\FixtureModel;
use MageWorkshop\DetailedReview\Helper\Attribute;
use Magento\Framework\Model\AbstractModel;

abstract class AbstractFixturesCommand extends \Symfony\Component\Console\Command\Command
{
    const RANDOM_TEXT = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum';

    const EXCEPTION_CANNOT_GENERATE_FIXTURES = "Can't generate fixtures for '%1' input type!";

    protected $firstNames = [
        'Jaleesa', 'Marlon', 'Marion', 'Princess', 'Alyse', 'Despina', 'Loida', 'Tim', 'Hilton',
        'Priscilla', 'Harland', 'Coreen', 'Chanda', 'Agueda', 'Suzann', 'Katerine', 'Adrianna', 'Monte', 'Veronica',
        'Genesis', 'Joni', 'Chung', 'Valorie', 'Elizabet', 'Echo', 'Doria', 'Odelia', 'Yer', 'Nathan', 'Cristie',
        'Margie', 'Merry', 'Janene', 'Bridgette', 'See', 'Stephaine', 'Estrella', 'Dianne', 'Margarita', 'Lucas',
        'Glenn', 'Gerda', 'Kieth', 'Flo', 'Antoine', 'Nena', 'Rolando', 'Nila', 'Lashon', 'Yesenia'
    ];

    protected $lastNames = [
        'Hawthorne', 'Melcher', 'Palmer', 'Katzman', 'Viviani', 'Omundsen', 'Ferren', 'Kayyali',
        'Mieher', 'Delozier', 'Tsomides', 'Blenis', 'Moreno-leon', 'Epps', 'Brimacombe', 'Krause', 'Distel', 'Lenares',
        'Emard', 'Jewett', 'Belich', 'Gilligan', 'Vandenbroek', 'Affricano', 'Gerardi', 'Flood', 'Parr', 'Kadner',
        'Arrellanes', 'Slowe', 'Putnoi', 'Humber', 'Rindone', 'Englehardt', 'Caperton', 'Guido', 'Nicholls',
        'Quatromini', 'Ji', 'Storer', 'Seltzer', 'Bassett', '', 'Weingarten', 'Chappell', 'Lewin', 'Mcginnes',
        'Tibbitts', 'Gropper', 'Steedly'
    ];

    /** @var array $randomText */
    protected $randomText;

    /** @var \Magento\Eav\Model\Config $eavConfig */
    protected $eavConfig;

    /** @var \Magento\Framework\App\State $appState */
    protected $appState;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\State $appState
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\State $appState,
        $name = null
    ) {
        parent::__construct($name);
        $this->appState = $appState;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode(FixtureModel::AREA_CODE);
    }

    /**
     * @param $totalItemsProcessed
     * @param $count
     * @param OutputInterface $output
     */
    protected function logTotal($totalItemsProcessed, $count, OutputInterface $output)
    {
        $time = date('H:i:s');
        $output->writeln("<info>$time | Total items processed: $totalItemsProcessed out of $count</info>");
    }

    /**
     * @param AbstractModel $entity
     * @param array $attributes
     * @throws \Exception
     */
    protected function populateData(AbstractModel $entity, $attributes)
    {
        foreach ($attributes as $attributeCode => $attributeData) {
            $data = null;

            switch ($attributeData['input_type']) {
                case 'text':
                    switch ($attributeCode) {
                        case 'location':
                            $data = 'Test Location ' . uniqid();
                            break;
                        case 'age':
                            $data = mt_rand(18, 90);
                            break;
                        case 'height':
                            $data = mt_rand(120, 200);
                            break;
                        case 'nickname':
                            $data = $this->generateNickname();
                            break;
                        default:
                            $data = $this->generateParagraph(2, 3);
                    }
                    break;
                case 'textarea':
                    $data = $this->generateText();
                    break;
                case 'boolean':
                    // should be casted to string - otherwise NULL is saved
                    $data = (string) mt_rand(Attribute::BOOLEAN_NO_OPTION, Attribute::BOOLEAN_YES_OPTION);
                    break;
                case 'select':
                    $data = null;
                    $canBeEmpty = (isset($attributeData['can_be_empty']) && $attributeData['can_be_empty']);
                    if (!$canBeEmpty || ($canBeEmpty && !empty($attributeData['options']) && !mt_rand(0, 1))) {
                        $randomKeys = array_rand($attributeData['options']);
                        $data = $attributeData['options'][$randomKeys];
                    }
                    break;
                case 'multiselect':
                    $keysToChoose = ceil(count($attributeData['options']) / 2);
                    $randomKeys = array_rand($attributeData['options'], $keysToChoose);
                    $data = [];
                    foreach ($randomKeys as $randomKey) {
                        $data[] = $attributeData['options'][$randomKey];
                    }
                    $data = implode(',', $data);
                    break;
                default:
//                    throw new LocalizedException(
//                        __(self::EXCEPTION_CANNOT_GENERATE_FIXTURES, $attributeData['input_type'])
//                    );
            }

            if ($data !== null) {
                $entity->setData($attributeCode, $data);
            }
        }
    }

    /**
     * @param int $length
     * @return string
     */
    protected function generateText($length = 5)
    {
        $text = [];
        while ($length > 0) {
            $text[] = $this->generateParagraph();
            $length--;
        }
        return implode("\n", $text);
    }

    /**
     * @param int $minLength
     * @param int $maxLength
     * @return string
     */
    protected function generateParagraph($minLength = 5, $maxLength = 12)
    {
        $sentencesToGenerate = mt_rand($minLength, $maxLength);
        $dot = '.';
        $phrase = [];
        while ($sentencesToGenerate--) {
            $phrase[] = ucfirst($this->generateRandomSentences());
        }
        return implode(' ', $phrase) . $dot;
    }

    /**
     * @param int $minSentenceLength
     * @param int $maxSentenceLength
     * @return string
     */
    protected function generateRandomSentences($minSentenceLength = 5, $maxSentenceLength = 15)
    {
        $randomText = $this->getRandomText();
        $randomTextLength = count($randomText);
        $sentenceLength = mt_rand($minSentenceLength, $maxSentenceLength);
        $startPos = mt_rand(0, $randomTextLength - 1);

        $generatedSentence = array_slice($randomText, $startPos, $sentenceLength);
        if (count($generatedSentence) < $sentenceLength) {
            $generatedSentence = array_merge(
                $generatedSentence,
                array_slice($randomText, 0, $sentenceLength - count($generatedSentence))
            );
        }

        return implode(' ', $generatedSentence) . '.';
    }

    /**
     * @return string
     */
    protected function generateNickname()
    {
        $firstNamesCount = count($this->firstNames);
        $lastNamesCount = count($this->lastNames);
        return $this->firstNames[mt_rand(0, $firstNamesCount - 1)] . ' '
            . $this->lastNames[mt_rand(0, $lastNamesCount - 1)];
    }

    /**
     * @return array
     */
    protected function getRandomText()
    {
        if (null === $this->randomText) {
            $this->randomText = explode(' ', self::RANDOM_TEXT);
        }
        return $this->randomText;
    }
}
