<?php

namespace aetchell\Highcharts\Elemental {

use aetchell\Highcharts\Elemental\ElementalHighchart;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

    class ElementalHighchartSeries extends DataObject {

        private static $db = [
            'Title' => 'Varchar(255)',
            'SeriesType' => 'Varchar(50)',
            'Label' => 'Varchar(255)',
            'ValuePrefix' => 'Varchar(20)',
            'ValueSuffix' => 'Varchar(20)',
            'ShowYAxis' => 'Boolean',
            'ShowYAxisPosition' => 'Enum(array("left","right"),"left")',
            'Marker' => 'Boolean',
            'MarkerSymbol' => 'Enum(array("circle","square","triangle","triangle-down"),"circle")',
            'ConnectNulls' => 'Boolean',
            'Visible' => 'Boolean',
            'SeriesOrder' => 'Int',
        ];
        private static $has_one = [
            'ElementParent' => ElementalHighchart::class
        ];
        private static $table_name = 'ElementalHighchartSeries';
        private static $defaults = [
            'SeriesType' => 'line',
            'ShowYAxisPosition' => 'left',
            'Marker' => true,
            'MarkerSymbol' => 'circle',
            'Visible' => true
        ];
        private static $singular_name = 'Series';
        private static $plural_name = 'Series';
        private static $summary_fields = [
            'TitleNice' => 'Title',
            'DataPointMarkerNice' => 'Data point markers',
            'SeriesType' => 'Series type',
            'ShowYAxisNice' => 'Show Y axis',
            'LabelNice' => 'Label',
            'ValuePrefix' => 'Value prefix',
            'ValueSuffix' => 'Value suffix',
            'VisibleNice' => 'Visible'
            
        ];

        /**
         * Helper fields
         */
        public function SeriesLabel() {
            if (!empty($this->Label) && $this->Label !== '') {
                return $this->Label;
            }

            if (!empty($this->ElementParent()->DefaultSeriesLabel) && trim($this->ElementParent()->DefaultSeriesLabel) !== '') {
                return $this->ElementParent()->DefaultSeriesLabel;
            }
            return false;
        }

        public function SeriesTitle() {
            if (!empty($this->Title) && $this->Title !== '') {
                return $this->Title;
            }

            if (!empty($this->ElementParent()->DefaultSeriesTitle) && trim($this->ElementParent()->DefaultSeriesTitle) !== '') {
                return $this->ElementParent()->DefaultSeriesTitle;
            }
            return false;
        }

        /**
         * Summary field helper methods
         * @return string
         */
        public function TitleNice() {
            if ($this->Title == true) {
                return $this->Title;
            }
            return DBField::create_field('HTMLText', '<i>none</i>');
        }

        public function LabelNice() {
            if ($this->Label == true) {
                return $this->Label;
            }
            return DBField::create_field('HTMLText', '<i>none</i>');
        }

        public function ShowYAxisNice() {
            if ($this->ShowYAxis == true) {
                return $this->ShowYAxisPosition;
            }
            return DBField::create_field('HTMLText', '<i>no</i>');
        }

        public function VisibleNice() {
            if ($this->Visible == true) {
                return 'yes';
            }
            return 'no';
        }        
        
        public function DataPointMarkerNice() {
            if ($this->Marker == true) {
                return $this->MarkerSymbol;
            }
            return DBField::create_field('HTMLText', '<i>no</i>');
        }

        public function getCMSFields() {
            $fields = parent::getCMSFields();
            $fields->removeByName('SeriesOrder');
            $fields->removeByName('ElementParentID');
            $fields->removeByName('Title');
            $fields->removeByName('SeriesType');
            $fields->removeByName('Label');
            $fields->removeByName('ValuePrefix');
            $fields->removeByName('ValueSuffix');
            $fields->removeByName('ShowYAxis');
            $fields->removeByName('ShowYAxisPosition');
            $fields->removeByName('ConnectNulls');

            $fields->removeByName('Marker');
            $fields->removeByName('MarkerSymbol');
            $fields->removeByName('Visible');

            $SeriesTypes = $this->ElementParent()->dbObject('DefaultSeries')->enumValues();

            $SeriesNotes = LiteralField::create('SeriesNotes', '<p><strong>Note</strong> that the Y axis for the first series in the list will <strong>always</strong> be shown regardless of options selected.</p>');
            $Title = TextField::create('Title', 'Series title')->setAttribute('placeholder', 'rainfall')->setDescription('The Y axis title for the series. This is the title of the data being meassured, for example "rainfall"');
            $Label = TextField::create('Label', 'Series label')->setAttribute('placeholder', 'mm')->setDescription('The Y axis label, this appears next to the Y axis value that is being measured, for example "mm" if the series is rainfall.');

            $ValuePrefix = TextField::create('ValuePrefix', 'Value prefix');
            $ValueSuffix = TextField::create('ValueSuffix', 'Value suffix');

            $SeriesType = DropdownField::create('SeriesType', 'Series type')->setSource($SeriesTypes);
            $SeriesType->setDescription('This will override the default charts series type. Note that not all types are compatible, Bar and Column for example.');

            $ShowYAxis = CheckboxField::create('ShowYAxis', 'Show the Y axis for this series');
            $ShowYAxis->setDescription('Show the Y axis for this series. Note that the Y axis for the first series in the list will always be shown.');
            $ShowYAxisPosition = OptionsetField::create('ShowYAxisPosition', 'Y Axis position')->setSource($this->dbObject('ShowYAxisPosition')->enumValues());

            $Visible = CheckboxField::create('Visible', 'Visible');
            $Visible->setDescription('Sets the initial visibility of the series. uncheck this to have the series disabled when the chart first loads.');


            $MarkerNotes = LiteralField::create('MarkerNotes', '<div>These options are only availble for "line","spline", "area" and "areaspline" series.</div>');
            $Marker = CheckboxField::create('Marker', 'Show data point markers');
            $Marker->setDescription('Show markers on each data point. Markers are only available for certain series types such as "line" and "spline"');
            $Marker->displayIf('SeriesType')->isEqualTo('line')
                    ->orIf('SeriesType')->isEqualTo('spline')
                    ->orIf('SeriesType')->isEqualTo('areaspline')
                    ->orIf('SeriesType')->isEqualTo('area');
            $MarkerSymbol = DropdownField::create('MarkerSymbol', 'Marker symbol')->setSource($this->dbObject('MarkerSymbol')->enumValues());
            $MarkerSymbol->setDescription('Which symbol to use for the data point marker.');
            $MarkerSymbol->displayIf('Marker')->isChecked();

            if ($this->ID > 0) {
                $ParentType = LiteralField::create('ParentChartType', '<p>Parent charts default series type is <strong>' . $this->ElementParent()->LibType . ' / ' . $this->ElementParent()->DefaultSeries . '</strong></p>');
                $fields->addFieldToTab('Root.Main', $ParentType);
            }


            $fields->addFieldToTab('Root.Main', $SeriesNotes);
            $fields->addFieldToTab('Root.Main', $Title);
            $fields->addFieldToTab('Root.Main', $Label);
            $fields->addFieldToTab('Root.Main', CompositeField::create(FieldGroup::create(
                                    $ValuePrefix,
                                    $ValueSuffix
                    ))->setTitle('Series data formatting'));
            $fields->addFieldToTab('Root.Main', CompositeField::create(
                    $SeriesType,
                    $Visible
                    )->setTitle('Series Attributes'));
            $fields->addFieldToTab('Root.Main', CompositeField::create($ShowYAxis, $ShowYAxisPosition)->setTitle('Y Axis'));

            $fields->addFieldToTab('Root.Main', CompositeField::create($MarkerNotes, $Marker, $MarkerSymbol)->setTitle('Series marker'));
            return $fields;
        }

        public function canView($member = null) {
            return true;
        }

        public function canEdit($member = null) {
            if (!$member) {
                if (!Security::getCurrentUser()) {
                    return false;
                }
                $member = Security::getCurrentUser();
            }
            return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
        }

        public function canDelete($member = null) {
            if (!$member) {
                if (!Security::getCurrentUser()) {
                    return false;
                }
                $member = Security::getCurrentUser();
            }
            return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
        }

        public function canCreate($member = null, $context = []) {
            if (!$member) {
                if (!Security::getCurrentUser()) {
                    return false;
                }
                $member = Security::getCurrentUser();
            }
            return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
        }

    }

}