<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ts
 * @author     Percept <perceptinfotech2@gmail.com>
 * @copyright  2023 Percept
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Teamtournaments\Component\Ts\Administrator\Field;

defined('JPATH_BASE') or die;

use DateTime;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

/**
 * Supports an HTML select list of categories
 *
 * @since  1.0.0
 */
class CalendartimeonlyField extends \Joomla\CMS\Form\Field\CalendarField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'calendartimeonly';

    protected $getdatefrom = '';

    public function __get($name)
    {
        switch ($name) {
            case 'getdatefrom':
                return $this->$name;
        }

        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'getdatefrom':
                $this->$name = (string) $value;
                break;

            default:
                parent::__set($name, $value);
        }
    }

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);
        if($return){
            $this->getdatefrom = (string) $this->element['getdatefrom'];
        }
        return $return;
    }

    protected function getLayoutData()
    {
        $data      = parent::getLayoutData();
        $extraData = [
            'getdatefrom'  => $this->getdatefrom
        ];  
        return array_merge($data, $extraData);
    }

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string    The field input markup.
	 *
	 * @since   1.0.0
	 */
    protected function getInput()
	{
		$user = Factory::getApplication()->getIdentity();

        // If a known filter is given use it.
        switch (strtoupper($this->filter)) {
            case 'SERVER_UTC':
                // Convert a date to UTC based on the server timezone.
                if ($this->value && $this->value != $this->getDatabase()->getNullDate()) {
                    // Get a date object based on the correct timezone.
                    $date = Factory::getDate($this->value, 'UTC');
                    $date->setTimezone(new \DateTimeZone(Factory::getApplication()->get('offset')));

                    // Transform the date string.
                    $this->value = $date->format('Y-m-d H:i:s', true, false);
                }
                break;
            case 'USER_UTC':
                // Convert a date to UTC based on the user timezone.
                if ($this->value && $this->value != $this->getDatabase()->getNullDate()) {
                    // Get a date object based on the correct timezone.
                    $date = Factory::getDate($this->value, 'UTC');
                    $date->setTimezone($user->getTimezone());

                    // Transform the date string.
                    $this->value = $date->format('Y-m-d H:i:s', true, false);
                }
                break;
        }

        // Format value when not nulldate ('0000-00-00 00:00:00'), otherwise blank it as it would result in 1970-01-01.
        if ($this->value && $this->value != $this->getDatabase()->getNullDate() && strtotime($this->value) !== false) {
            $tz = date_default_timezone_get();
            date_default_timezone_set('UTC');

            if ($this->filterFormat) {
                $date        = \DateTimeImmutable::createFromFormat('U', strtotime($this->value));
                $this->value = $date->format($this->filterFormat);
            } else {
                $this->value = strftime($this->format, strtotime($this->value));
            }

            date_default_timezone_set($tz);
        } else {
            $this->value = '';
        }
        extract($this->getLayoutData());
        $document = Factory::getApplication()->getDocument();
        $lang     = Factory::getApplication()->getLanguage();

        $inputvalue = '';

        // Build the attributes array.
        $attributes = [];

        empty($size)      ? null : $attributes['size'] = $size;
        empty($maxlength) ? null : $attributes['maxlength'] = $maxLength;
        empty($class)     ? $attributes['class'] = 'form-control' : $attributes['class'] = 'form-control ' . $class;
        !$readonly        ? null : $attributes['readonly'] = 'readonly';
        !$disabled        ? null : $attributes['disabled'] = 'disabled';
        empty($onchange)  ? null : $attributes['onchange'] = $onchange;

        if ($required) {
            $attributes['required'] = '';
        }

        // Handle the special case for "now".
        if (strtoupper($value) === 'NOW') {
            $value = Factory::getDate()->format('Y-m-d H:i:s');
        }

        $readonly = isset($attributes['readonly']) && $attributes['readonly'] === 'readonly';
        $disabled = isset($attributes['disabled']) && $attributes['disabled'] === 'disabled';

        if (is_array($attributes)) {
            $attributes = ArrayHelper::toString($attributes);
        }
        
        $calendarAttrs = [
            'data-inputfield'      => $id,
            'data-button'          => $id . '_btn',
            'data-date-format'     => $format,
            'data-firstday'        => empty($firstday) ? '' : $firstday,
            'data-weekend'         => empty($weekend) ? '' : implode(',', $weekend),
            'data-today-btn'       => $todaybutton,
            'data-week-numbers'    => $weeknumbers,
            'data-show-time'       => $showtime,
            'data-show-others'     => $filltable,
            'data-time24'          => $timeformat,
            'data-only-months-nav' => $singleheader,
            'data-min-year'        => $minYear,
            'data-max-year'        => $maxYear,
            'data-date-type'       => strtolower($calendar),
            'data-getdatefrom'     => empty($getdatefrom) ? '' : $getdatefrom,
        ];

        $calendarAttrsStr = ArrayHelper::toString($calendarAttrs);

        // Add language strings
        $strings = [
            // Days
            'SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY',
            // Short days
            'SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT',
            // Months
            'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER',
            // Short months
            'JANUARY_SHORT', 'FEBRUARY_SHORT', 'MARCH_SHORT', 'APRIL_SHORT', 'MAY_SHORT', 'JUNE_SHORT',
            'JULY_SHORT', 'AUGUST_SHORT', 'SEPTEMBER_SHORT', 'OCTOBER_SHORT', 'NOVEMBER_SHORT', 'DECEMBER_SHORT',
            // Buttons
            'JCLOSE', 'JCLEAR', 'JLIB_HTML_BEHAVIOR_TODAY',
            // Miscellaneous
            'JLIB_HTML_BEHAVIOR_WK',
        ];

        foreach ($strings as $c) {
            Text::script($c);
        }

        // These are new strings. Make sure they exist. Can be generalised at later time: eg in 4.1 version.
        if ($lang->hasKey('JLIB_HTML_BEHAVIOR_AM')) {
            Text::script('JLIB_HTML_BEHAVIOR_AM');
        }

        if ($lang->hasKey('JLIB_HTML_BEHAVIOR_PM')) {
            Text::script('JLIB_HTML_BEHAVIOR_PM');
        }

        $script = '
            function handleDateChange(field){
                var timeString = field.value;
                var getDateFrom = field.getAttribute("data-getdatefrom");
                if(getDateFrom && timeString){
                    var gameDate = document.getElementById("jform_"+getDateFrom).value;
                    gameDate = gameDate.split(" ")[0];
                    timeString = timeString.split(" ")[1];

                    timeString = gameDate + " " + timeString;
                    field.value = timeString;
                    field.setAttribute("data-alt-value", timeString);
                }
                var dateTime = new Date(timeString);
                var hours = dateTime.getHours();
                var minutes = dateTime.getMinutes();                
                var ampm = hours >= 12 ? "PM" : "AM";
                hours = hours % 12;
                hours = hours ? hours : 12; // Handle midnight (0 hours)
                hours = hours < 10 ? "0" + hours : hours;      
                minutes = minutes < 10 ? "0" + minutes : minutes;                
                // Construct the time string in 12-hour format
                var time12HourFormat = hours + ":" + minutes + " " + ampm;
                document.getElementById("'.$id.'_customized").value = time12HourFormat;
                //console.log(field.value);alert("hello");
            }
        ';

        $style = '
            .field-calendar.hidedaysrows .daysrow{display:none !important;}
            .field-calendar.hidedaysrows .calendar-header{display:none !important;}
            .field-calendar.hidedaysrows .buttons-wrapper{display:none !important;}
            #'.$id.'_customized[readonly]{background-color: #fff;}
        ';

        // Redefine locale/helper assets to use correct path, and load calendar assets
        $document->getWebAssetManager()
            ->registerAndUseScript('field.calendar.helper', $helperPath, [], ['defer' => true])
            ->useStyle('field.calendar' . ($direction === 'rtl' ? '-rtl' : ''))
            ->useScript('field.calendar')
            ->addInlineScript($script)
            ->addInlineStyle($style);

        $fieldHtml = '
        <div class="field-calendar hidedaysrows">';
            if (!$readonly && !$disabled) : 
                $fieldHtml .= '<div class="input-group">';
            endif; 
            $time12HourFormat = '';
            if($value !== '0000-00-00 00:00:00' && $value !== ''){
                $dateTime = new DateTime($value);
                // Format the DateTime object to 12-hour format with AM/PM
                $time12HourFormat = $dateTime->format('h:i A');  
            }  
            $fieldHtml .= '<input
                    type="hidden"
                    id="'.$id.'"
                    name="'.$name.'"
                    value="'.htmlspecialchars(($value !== '0000-00-00 00:00:00') ? $value : '', ENT_COMPAT, 'UTF-8').'"
                    '. (!empty($description) ? ' aria-describedby="' . ($id ?: $name) . '-desc"' : '') .'
                    '. $attributes .'
                    '. ($dataAttribute ?? '').'
                    '. (!empty($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : '').'
                    data-alt-value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" autocomplete="off" data-getdatefrom="'.$getdatefrom.'" onchange="handleDateChange(this)">
                    <input readonly type="text" id="'.$id.'_customized" class="form-control inputbox" name="customized" value="'.$time12HourFormat.'" '. (!empty($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : '').'/>
                <button type="button" class="'.( ($readonly || $disabled) ? 'hidden ' : '').'btn btn-primary"
                    id="'.$id.'_btn"
                    title="'.Text::_('JLIB_HTML_BEHAVIOR_OPEN_CALENDAR').'"
                    '.$calendarAttrsStr.'
                ><span class="icon-calendar" aria-hidden="true"></span>
                <span class="visually-hidden">'.Text::_('JLIB_HTML_BEHAVIOR_OPEN_CALENDAR').'</span>
                </button>';
                if (!$readonly && !$disabled) : 
                    $fieldHtml .= '</div>';
                endif;
        $fieldHtml .= '</div>';
        return $fieldHtml;        
		//dd($this->layout, $this->getLayoutData());
        //return $this->getRenderer($layout)->render($this->getLayoutData())."test additional things";
	}
}
