<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       mark-gabinete
 * @since      1.0.0
 *
 * @package    Scientific_Citation
 * @subpackage Scientific_Citation/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Scientific_Citation
 * @subpackage Scientific_Citation/public
 * @author     Mark Gabinete <markgabinete02@gmail.com>
 */

if ( !class_exists( 'Scientific_Citation_Public' ) ) {
	class Scientific_Citation_Public {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;
				
		/**
		 * citation_list 
		 * This stores the citation list per shortcode added
		 * 
		 * @var array
		 */
		private $citation_list;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since   1.0.0
		 * @param   string    $plugin_name       The name of the plugin.
		 * @param   string    $version    		 The version of this plugin. 
		 * @var 	array	  $citation_list 	 The array list for citations.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version = $version;
			$this->citation_list = array();
			
		}

		
		/**
		 * Register the stylesheets for the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {

			/**
			 * Register public styles only
			 */
			wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/scientific-citation-public.css' );

		}

		/**
		 * Register the JavaScript for the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {

			/**
			 * Enqueue public scripts
			 */

			// wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/scientific-citation-public.js', array( 'jquery' ), $this->version, false );

		}
		
		
				
		/**
		 * scicit_generate_citation
		 * 
		 * @link https://codex.wordpress.org/Shortcode_API
		 * @param  mixed $atts 
		 * @param  string $content This is the String to be cited
		 * 
		 * @return string
		 */

		public function scicit_generate_citation($atts, $content = '') {
			static $superscript=0;
			static $citation_list = "";

			$attributes = shortcode_atts(array(
				'type' => '',
				'authors' => '',
				'article' => '',
				'book-title' => '',
				'date-of-access' => '',
				'edition' => '',
				'format' => '',
				'issueno' => '',
				'journal-title' => '',
				'magazine-title' => '',
				'pages' => '',
				'publication-year' => '',
				'place-of-publication' => '',
				'publication-date' => '',
				'place' => '',
				'publisher' => '',
				'page-title' => '',
				'url' => '',
				'volume' => '',
				'webpage-title' => '',
				'work-title' => '',
				), 
				$atts
			);

			
			$output = '';
			$section_anchor = "scicit_citations";

			//Checks if citation type is present
			if ( !$attributes['type'] ) {
				return "<p>Error: Empty citation type.<p>";
			}
			if ( $content == '' ) {
				return "empty";
			}

			// Counts the number of citation shortcode used
			$superscript++;
			$linknote = $section_anchor."-note-".$superscript;
			$linkref = $section_anchor."-ref-".$superscript;
			$output = "<span><i>".$content."</i><a href='#".$linknote."'><sup id=".$linkref.">[".$superscript."]</sup></a></span>";
			
			wp_enqueue_style( 'scientific-citation' );
			// Checks if citation type is for Journal
			if($attributes['type'] == "Journal"){
				
				$this->scicit_generate_journal_citation($attributes['authors'], $attributes['article'], $attributes['journal-title'], $attributes['volume'], $attributes['issueno'], $attributes['publication-year'], $attributes['pages'], $linkref, $linknote);
				
				return $output;
			}

			// Checks if citation type is for Books
			if($attributes['type'] == "Book"){
				
				$this->scicit_generate_book_citation($attributes['authors'], $attributes['book-title'], $attributes['edition'], $attributes['place-of-publication'], $attributes['publisher'], $attributes['publication-year'], $linkref, $linknote);
				
				return $output;
			}

			// Checks if citation type is for Magazine
			if($attributes['type'] == "Magazine"){

				$this->scicit_generate_magazine_citation($attributes['authors'], $attributes['article'], $attributes['magazine-title'], $attributes['publication-date'], $attributes['volume'],  $attributes['issueno'], $attributes['pages'], $linkref, $linknote);
				
				return $output;
			}

			// Checks if citation type is for Website
			if($attributes['type'] == "Website"){
				
				$this->scicit_generate_website_citation($attributes['authors'], $attributes['webpage-title'], $attributes['date-of-access'],  $attributes['url'], $attributes['publication-date'], $attributes['format'], $linkref, $linknote);

				return $output;
			}

			
			return $output;
		}

		
		/**
		 * scicit_generate_journal_citation
		 *
		 * @param  string $authors
		 * @param  string $article
		 * @param  string $journal_title
		 * @param  int $volume
		 * @param  int $issueno
		 * @param  mixed $publication_year
		 * @param  mixed $pages
		 * @param  string $linkref
		 * @param  string $linknote
		 * @return void
		 */
		public function scicit_generate_journal_citation($authors, $article, $journal_title, $volume, $issueno, $publication_year, $pages, $linkref, $linknote){

			
			$author_array = $this->get_authors($authors);
			$scientific_citation_style = get_option( 'citation_style' );

			
			$citation_line = "";
			$c_authors = "";
			$authors_count = count($author_array);

			/**
			 *  APA (American Psychological Association) Citation Style
			 * 
			 *  Format: Author(s) of the article. (Year of publication). Title of the research article. Title of periodical, Volume number(Issue number), Page numbers.
			 */
			if($scientific_citation_style == 'apa'){
				$c_authors = $this->get_authors_format_apa($authors_count, $author_array);
				
				// Check if $issueno is not empty , if not add ()
				if($issueno != ""){
					$issueno = "( ".$issueno." )";
				}
				
				$citation_line .= "<li id=".$linknote."><span><b><a href='#".$linkref."' aria-label='Jump up' title='Jump up'>^</a></b></span> ".$c_authors."( ".$this->get_dates($publication_year)." ). ".$article;
				$citation_line .= ". <i>".$journal_title."</i>, <i>".$volume."</i> ".$issueno." , ".$pages.". </li>";

				return array_push($this->citation_list,$citation_line);
			}

			/**
			 * Chicago/Turabian Citation Style
			 * 
			 * Format: Author(s) of the article. "Title of the article." Title of the journal Volume number, no. Issue number (Year of publication): page range.
			 * 
			 */
			if($scientific_citation_style == 'chicago'){

				$c_authors = $this->get_authors_format_chicago($authors_count, $author_array);

				// Check if $issueno is not empty , if not add no. in the start
				if($issueno != ""){
					$issueno = "no. ".$issueno;
				}
				
				// check if pages is added, if true add colon":"
				if($pages != ""){
					$pages = ":".$pages;
				}
				$citation_line .= "<li id=".$linknote."><span><b><a href='#".$linkref."' aria-label='Jump up' title='Jump up'>^</a></b></span> ".$c_authors." \"".$article.".\" ".$journal_title." ".$volume.", ".$issueno;
				$citation_line .= " ( ".$this->get_dates($publication_year)." )".$pages;

				return array_push($this->citation_list,$citation_line);
			}

		}

				
		/**
		 * scicit_generate_book_citation
		 *
		 * @param  mixed $authors
		 * @param  mixed $book_title
		 * @param  mixed $edition
		 * @param  mixed $place_of_publication
		 * @param  mixed $publisher
		 * @param  mixed $publication_year
		 * @param  mixed $linkref
		 * @param  mixed $linknote
		 * @return void
		 */
		public function scicit_generate_book_citation($authors, $book_title, $edition, $place_of_publication, $publisher, $publication_year, $linkref, $linknote){

			$author_array = $this->get_authors($authors);
			$scientific_citation_style = get_option( 'citation_style' );

			
			$citation_line = "";
			$c_authors = "";
			$authors_count = count($author_array);

			/**
			 *  APA (American Psychological Association) Citation Style
			 * 
			 *  Format: Author(s) of the book. (Year of publication). Title of the book. (Edition number ed.). Place of publication: Publisher.
			 * 
			 */
			if($scientific_citation_style == 'apa'){

				$c_authors = $this->get_authors_format_apa($authors_count, $author_array);

				//check if edition is not empty
				if($edition != ""){
					$edition = "( ".$edition." ).";
				}

				//check if publisher is not empty
				if($publisher != "") {
					$publisher = ": ".$publisher;
				}

				$citation_line .= "<li id=".$linknote."><span><b><a href='#".$linkref."' aria-label='Jump up' title='Jump up'>^</a></b></span> ".$c_authors." ( ".$this->get_dates($publication_year)." ). ";
				$citation_line .= $book_title.". ".$edition." ".$place_of_publication.$publisher;
				return array_push($this->citation_list,$citation_line);

			}

			/**
			 * Chicago/Turabian Citation Style
			 * 
			 * Format: Author(s) of the book. Title of the book. Place of publication: Publisher, Year of publication.
			 * 
			 */
			if($scientific_citation_style == 'chicago'){
				$c_authors = $this->get_authors_format_chicago($authors_count, $author_array);

				//check if publisher is not empty
				if($publisher != "") {
					$publisher = ": ".$publisher;
				}

				$citation_line .= "<li id=".$linknote."><span><b><a href='#".$linkref."' aria-label='Jump up' title='Jump up'>^</a></b></span> ".$c_authors." ".$book_title.". ".$place_of_publication.$publisher.", ".$this->get_dates($publication_year);
				return array_push($this->citation_list,$citation_line);
			}

			
		}
		
		/**
		 * scicit_generate_magazine_citation
		 *
		 * @param  mixed $authors
		 * @param  mixed $article
		 * @param  mixed $magazine_title
		 * @param  mixed $publication_date
		 * @param  mixed $volume
		 * @param  mixed $issueno
		 * @param  mixed $pages
		 * @param  mixed $linkref
		 * @param  mixed $linknote
		 * @return void
		 */
		public function scicit_generate_magazine_citation($authors, $article, $magazine_title, $publication_date, $volume, $issueno, $pages, $linkref, $linknote){

			$author_array = $this->get_authors($authors);
			$scientific_citation_style = get_option( 'citation_style' );

			
			$citation_line = "";
			$c_authors = "";
			$authors_count = count($author_array);

			/**
			 *  APA (American Psychological Association) Citation Style
			 * 
			 *  Format: Author(s) of the article. (Year, Month of publication). Title of the article. Title of the magazine, Volume number(Issue number), Page numbers.
			 *  
			 */
			if($scientific_citation_style == 'apa'){
				$c_authors = $this->get_authors_format_apa($authors_count, $author_array);

				
				$citation_line .= "<li id=".$linknote."><span><b><a href='#".$linkref."' aria-label='Jump up' title='Jump up'>^</a></b></span> ".$c_authors."( ".$this->get_dates($publication_date)." ). ".$article;
				$citation_line .= ". <i>".$magazine_title."</i>, <i>".$volume."</i> ".$issueno." , ".$pages.". </li>";

				return array_push($this->citation_list,$citation_line);

			}

			/**
			 * Chicago/Turabian Citation Style
			 * 
			 * Format: Author(s) of the article. "Title of the article." Title of the magazine. Date of publication.
			 * 
			 */
			if($scientific_citation_style == 'chicago'){
				$c_authors = $this->get_authors_format_chicago($authors_count, $author_array);

				$citation_line .= "<li id=".$linknote."><span><b><a href='#".$linkref."' aria-label='Jump up' title='Jump up'>^</a></b></span> ".$c_authors." \"".$article.".\" ".$magazine_title.". ".$publication_date;
				
				return array_push($this->citation_list,$citation_line);
			}
			

		}
		
		/**
		 * scicit_generate_website
		 *
		 * @param  mixed $authors
		 * @param  mixed $publisher
		 * @param  mixed $webpage_title
		 * @param  mixed $date_of_access
		 * @param  mixed $url
		 * @param  mixed $publication_date
		 * @param  mixed $format
		 * @param  mixed $linkref
		 * @param  mixed $linknote
		 * @return void
		 */
		public function scicit_generate_website_citation($authors, $webpage_title, $date_of_access, $url, $publication_date, $format , $linkref, $linknote){
			//[citation type="Website" author="" publisher="" webpage-title="" date-of-access="" url="" publication-date="" format=""]Text to be cited[/citation]s

			$author_array = $this->get_authors($authors);
			$scientific_citation_style = get_option( 'citation_style' );

			
			$citation_line = "";
			$c_authors = "";
			$authors_count = count($author_array);

			/**
			 *  APA (American Psychological Association) Citation Style
			 * 
			 *  Format: Author(s) of the webpage. (Date of publication). Title of the webpage [Format]. Retrieved from URL
			 *  
			 */
			if($scientific_citation_style == 'apa'){
				$c_authors = $this->get_authors_format_apa($authors_count, $author_array);
				
				// Check if $issueno is not empty , if not add ()
				if($format != ""){
					$format = "[ ".$format." ]";
				}
				
				$citation_line .= "<li id=".$linknote."><span><b><a href='#".$linkref."' aria-label='Jump up' title='Jump up'>^</a></b></span> ".$c_authors."( ".$this->get_dates($publication_date)." ). ".$webpage_title;
				$citation_line .= " ".$format.". ".$url;

				return array_push($this->citation_list,$citation_line);
			}

			/**
			 * Chicago/Turabian Citation Style
			 * 
			 * Format: Author(s) of the website. "Title of the page." Accessed Date of access. URL.
			 * 
			 */
			if($scientific_citation_style == 'chicago'){
				$c_authors = $this->get_authors_format_chicago($authors_count, $author_array);
				
				// Check if $issueno is not empty , if not add ()
				if($format != ""){
					$format = "[ ".$format." ]";
				}
				// Check if date access is not empty
				if($date_of_access != ""){
					$date_of_access = "Accessed ".$date_of_access.".";
				}
				$citation_line .= "<li id=".$linknote."><span><b><a href='#".$linkref."' aria-label='Jump up' title='Jump up'>^</a></b></span> ".$c_authors.". \"".$webpage_title.".\" ".$date_of_access." ".$url;

				return array_push($this->citation_list,$citation_line);
			}


		}

		/**
		 * get_authors
		 *
		 * @param  string $author_string
		 * @return array Returns an array of authors
		 */
		public function get_authors($author_string){
			$author_array = explode("|" , $author_string);
			return $author_array;
		}

		/**
		 * get_authors_format_apa
		 * 
		 * APA (American Psychological Association) Citation Style
		 * 
		 * Guide for Authors: Give the last name and initials (e. g. Watson, J. D.) of up to 20 authors with the last name preceded by an ampersand (&). 
		 * For 21 or more authors include the first 19 names followed by an ellipsis (…) and add the last author's name.
		 * 
		 * @param  int $authors_count
		 * @param  array $author_array
		 * @return string $c_authors Formated Authors based on APA style
		 */
		public function get_authors_format_apa($authors_count, $author_array){

			$c_authors = "";
			$i=1;
			
			foreach ($author_array as $key => $value) {
				if($authors_count > 20 ){
					if($i<19){
						$c_authors .= $value.", &";
					}
					if($i==19){
						$c_authors .= $value.", ...";

					}
				}else if($authors_count <= 20){
					if($i<$authors_count){
						$c_authors .= $value.", &";
					}
				}


				if($i == $authors_count){
					$c_authors .= $value.".";
				}
				$i++;
				
			}

			return $c_authors;
		}
		
		/**
		 * get_authors_format_chicago
		 * 
		 * Chicago/Turabian Citation Style
		 * 
		 * Guide for Authors: Give first the last name, then the name as presented in the source (e. g. Watson, John). For two authors, reverse only the first name, followed by ‘and’
		 * and the second name in normal order (e. g. Watson, John, and John Watson). For more than seven authors, list the first seven names followed by et al.
		 * 
		 * @param  mixed $authors_count
		 * @param  mixed $author_array
		 * @return string $c_authors Formated Authors based on Chicago/Turabian Citation Style
		 */
		public function get_authors_format_chicago($authors_count, $author_array){

			$c_authors = "";
			$i=1;

			foreach ($author_array as $key => $value) {

				if($authors_count <= 2){
					if($i==1){
						$c_authors .= $value.", and ";
					}
					
				}else if($authors_count > 2 && $authors_count <= 7 ){
					if($i<6 && $i < $authors_count-1){
						$c_authors .= $value.", ";
					}
					if($i == $authors_count-1){
						$c_authors .= $value.", and ";
					}
					
				}else if($authors_count > 7){
					if($i<6){
						$c_authors .= $value.", ";
					}
					if($i == 6){
						$c_authors .= $value.", and ";
					}
					if($i == 7){
						$c_authors .= $value.", et al.";
						break;
					}
				}

				if($i == $authors_count){
					$c_authors .= $value.".";
				}
				$i++;

			}
			return $c_authors;
		}
		
		/**
		 * get_dates
		 *
		 * @param  mixed $cDate
		 * @return string Returns n.d if date is empty
		 */
		public function get_dates($cDate) {
			//Check if $publication_year is empty
			if($cDate == ""){
				return "n.d.";
			}
			return $cDate;
		}
		
		/**
		 * scicit_generate_citation_list
		 * 
		 * Iterates thru the citation list array and generate html elements for each citation item
		 * 
		 * @param  mixed $content
		 * @return string HTML content for the_content filter. priority 20
		 */
		public function scicit_generate_citation_list( $content) {    
			
			$session_array = $this->citation_list;
			$after_content = "<div class='scicit_cointainer'><h3 class='scicit_list_title'>Referrences</h3><hr>";
			if( is_single() ) {
				
				$after_content .= "<ol class='scicit_ol_list'>";
				foreach ($session_array as $key => $value) {
					$after_content .= $value;
				}
				$after_content .= "</ol>";
				$content .= $after_content;
			}
			$content .= "</div>";

			return $content;
		}

	}
}


