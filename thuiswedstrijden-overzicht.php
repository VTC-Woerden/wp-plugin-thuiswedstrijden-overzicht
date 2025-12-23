<?php
add_shortcode('matches_overview', 'matches_overview');
function matches_overview($atts = [], $content = null) {
	$week_days = ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'];
	$months_fullname = ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"];
	
	$xml = simplexml_load_file('https://api.nevobo.nl/export/sporthal/WOEPO/programma.rss', 'SimpleXMLElement', LIBXML_NOCDATA);
	
	if (!$xml) {
		return 'Geen wedstrijden gevonden.';
	}
	
	$matches = [];
	$current_date = new DateTime("today midnight", new DateTimeZone('Europe/Amsterdam'));
	
	$match_week = -1;
	foreach ($xml->channel->item as $match) {
		if (str_contains($match->title, 'Vervallen')) { 
			continue;
		}
		
		preg_match('/^(.{3}), (\d{2}) (.{3}) (\d{4}) (\d{2}:\d{2}):\d{2}.*$/', $match->pubDate, $regexResultsDate);
		$date = date_create_from_format('jMYH:i', $regexResultsDate[2].$regexResultsDate[3].$regexResultsDate[4].$regexResultsDate[5], new DateTimeZone('Europe/Amsterdam'));

		// Select the first match that is after the current date, then select only the matches in that week
		if ($date > $current_date) {
			if ($match_week == -1) {
				 $match_week = $date->format('W');
			}
			
			if ($match_week == $date->format('W')) {
				preg_match('/: (.*?) -+? (.*)$/', $match->title, $regexResultsTitle);
				array_push($matches, array('date' => $date, 'home' => $regexResultsTitle[1], 'guests' => $regexResultsTitle[2]));
			}
		}
	}
	
	$html = "";
	$current_day = "";
	foreach ($matches as $index=>$match) {
		$match_day = $week_days[$match['date']->format('w')] . ' ' . $match['date']->format('d') . ' ' . $months_fullname[$match['date']->format('n') - 1];
		
		if ($current_day != $match_day) {
			$current_day = $match_day;
			if ($index != 0) $html .= "</div>";
			$html .= "<h3 class='match_day_heading'>".$current_day."</h3>";
			$html .= "<div class='match_day'>";
		}
		
		$html .= <<<HTML
		<div class="match_day__match">
			<div class="match_day__home">{$match['home']}</div>
			<div class="match_day__time">{$match['date']->format('H:i')}</div>
			<div class="match_day__guests">{$match['guests']}</div>
		</div>
HTML;
	}
	
	return $html;
}
