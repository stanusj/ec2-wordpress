<?php
class UltimatumContent extends WP_Widget {
	/*
	 * Tricky Loops v5 Thanks to Richard
	*/
function UltimatumContent() {
        parent::WP_Widget(false, $name = 'WordPress Default Loop');
}


function widget($args, $instance) {
	/*
	 * Ult. 2.6 text Array
	 */
	 $instance['loop_text_vars'] =  array(
		 "Read More"        => __("Read More",'ultimatum'),
		 "More" 			=> __("More",'ultimatum'),
		 "Continue Reading"	=> __("Continue Reading",'ultimatum'),
		 "Continue"			=> __("Continue",'ultimatum'),
		 "Details" 			=> __("Details",'ultimatum'),
		 "daily"			=> __("Daily Archives %s","ultimatum"),
		 "monthly"			=> __("Monthly Archives %s","ultimatum"),
		 "yearly"			=> __("Yearly Archives %s","ultimatum"),
		 "archives"			=> __("Archives for %s","ultimatum"),
		 "author"			=> __("Posts by %s","ultimatum"),
		 "search"			=> __("Search Results for %s","ultimatum"),
	 		
	 );
    extract( $args );
    echo $before_widget;
    do_action('ultimatum_before_loop');
    do_action('ultimatum_loop',$args,$instance);
    do_action('ultimatum_after_loop');
    echo $after_widget;
    }

function update($new_instance, $old_instance) {
   $instance['single']        = $new_instance['single'];
   $instance['singlew']    = $new_instance['singlew'];
   $instance['singleh']    = $new_instance['singleh'];
   $instance['title']         = $new_instance['title'];
   $instance['meta']       = $new_instance['meta'];
   $instance['date']       = $new_instance['date'];
   $instance['author']        = $new_instance['author'];
   $instance['comments']      = $new_instance['comments'];
   $instance['cats']       = $new_instance['cats'];
   $instance['gallery']    = $new_instance['gallery'];
   $instance['imgpos']    = $new_instance['imgpos'];

   $instance['perpage']    = $new_instance['perpage'];
   $instance['mseperator']    = $new_instance['mseperator'];
   $instance['multiple']      = $new_instance['multiple'];
   $instance['multipleh']     = $new_instance['multipleh'];
   $instance['multiplew']     = $new_instance['multiplew'];
   $instance['atitle']        = $new_instance['atitle'];
   $instance['mtitle']        = $new_instance['mtitle'];
   $instance['mvideo']        = $new_instance['mvideo'];
   $instance['mmeta']         = $new_instance['mmeta'];
   $instance['mdate']         = $new_instance['mdate'];
   $instance['mauthor']    = $new_instance['mauthor'];
   $instance['mimgpos']    = $new_instance['mimgpos'];
   $instance['mcomments']     = $new_instance['mcomments'];
   $instance['mcats']         = $new_instance['mcats'];
   $instance['excerpt']    = $new_instance['excerpt'];
   $instance['excerptlength'] = $new_instance['excerptlength'];
   $instance['mreadmore']     = $new_instance['mreadmore'];
   $instance['rmtext']        = $new_instance['rmtext'];
   $instance['mmargin']    = $new_instance['mmargin'];
   $instance['mmseperator']   = $new_instance['mmseperator'];
   $instance['noimage']    = $new_instance['noimage'];
   $instance['mnoimage']      = $new_instance['mnoimage'];
   $instance['navigation']      = $new_instance['navigation'];
   $instance['show_comments_form']     = $new_instance['show_comments_form'];
   $instance['showtime']      = $new_instance['showtime'];
   $instance['mshowtime']     = $new_instance['mshowtime'];




     return $instance;
    }

function form($instance) {

      $single         = isset( $instance['single'] ) ? $instance['single'] : 'fimage';
      $title       = isset( $instance['title'] ) ? $instance['title'] : 'true';
      $excerpt     = isset( $instance['excerpt'] ) ? $instance['excerpt'] : 'true';
      $singlew    = isset( $instance['singlew'] ) ? $instance['singlew'] : '220';
      $singleh    = isset( $instance['singleh'] ) ? $instance['singleh'] : '220';
      $meta       = isset( $instance['meta'] ) ? $instance['meta'] : 'atitle';
      $mseperator    = isset( $instance['mseperator'] ) ? $instance['mseperator'] : '|';
      $date       = isset( $instance['date'] ) ? $instance['date'] : 'true';
      $author        = isset( $instance['author'] ) ? $instance['author'] : 'false';
      $comments      = isset( $instance['comments'] ) ? $instance['comments'] : 'true';
      $cats       = isset( $instance['cats'] ) ? $instance['cats'] : 'false';
      $gallery    = isset( $instance['gallery'] ) ? $instance['gallery'] : 'false';
      $imgpos       = isset( $instance['imgpos'] ) ? $instance['imgpos'] : 'btitle';
      $show_comments_form     = isset( $instance['show_comments_form'] ) ? $instance['show_comments_form'] : 'true';
      $atitle        = isset( $instance['atitle'] ) ? $instance['atitle'] : 'ON';
      $mtitle        = isset( $instance['mtitle'] ) ? $instance['mtitle'] : 'true';
      $mimgpos       = isset( $instance['mimgpos'] ) ? $instance['mimgpos'] : 'btitle';
      $mvideo        = isset( $instance['mvideo'] ) ? $instance['mvideo'] : 'false';
      $perpage    = isset( $instance['perpage'] ) ? $instance['perpage'] : '10';
      $multiple      = isset( $instance['multiple'] ) ? $instance['multiple'] : '1coli';
      $multiplew     = isset( $instance['multiplew'] ) ? $instance['multiplew'] : '220';
      $multipleh     = isset( $instance['multipleh'] ) ? $instance['multipleh'] : '220';
      $excerptlength    = isset( $instance['excerptlength'] ) ? $instance['excerptlength'] : '100';
      $mmeta         = isset( $instance['mmeta'] ) ? $instance['mmeta'] : 'atitle';
      $mmargin    = isset( $instance['mmargin'] ) ? $instance['mmargin'] : '30';
      $mdate         = isset( $instance['mdate'] ) ? $instance['mdate'] : 'true';
      $mauthor    = isset( $instance['mauthor'] ) ? $instance['mauthor'] : 'false';
      $mcomments     = isset( $instance['mcomments'] ) ? $instance['mcomments'] : 'true';
      $mcats         = isset( $instance['mcats'] ) ? $instance['mcats'] : 'false';
      $mreadmore     = isset( $instance['mreadmore'] ) ? $instance['mreadmore'] : 'right';
      $mmseperator   = isset( $instance['mmseperator'] ) ? $instance['mmseperator'] : '|';
      $rmtext        = isset( $instance['rmtext'] ) ? $instance['rmtext'] : 'Read More';
      $noimage    = isset( $instance['noimage'] ) ? $instance['noimage'] : 'true';
      $mnoimage      = isset( $instance['mnoimage'] ) ? $instance['mnoimage'] : 'true';
      $navigation      = isset( $instance['navigation'] ) ? $instance['navigation'] : 'numeric';


      $showtime      = isset( $instance['showtime'] ) ? $instance['showtime'] : '';
      $mshowtime     = isset( $instance['mshowtime'] ) ? $instance['mshowtime'] : '';



      $widget_id = $this->id;

      $tabdiv = "tabs-" . $widget_id;

      $tabsing = $tabdiv . "-single";
      $tabmulti = $tabdiv . "-multi";


      ?>
      <script>
      jQuery(function() {
         jQuery( "#<?php echo $tabdiv;?>" ).tabs();
      });
      </script>
      <div id="<?php echo $tabdiv;?>" >
      <ul>
         <li><a href="#<?php echo $tabsing;?>"><?php _e('Single Post Layout', 'ultimatum') ?></a></li>
         <li><a href="#<?php echo $tabmulti;?>"><?php _e('Multi Post Layout', 'ultimatum') ?></a></li>
      </ul>
      <div id="<?php echo $tabsing;?>">

      <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>">
      <option value="true" <?php selected($title,'true');?>>ON</option>
      <option value="false" <?php selected($title,'false');?>>OFF</option>
      </select>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('single'); ?>"><?php _e('Layout', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('single'); ?>" id="<?php echo $this->get_field_id('single'); ?>">
      <?php 
      		if(file_exists(THEME_LOOPS_DIR.'/extraloops.php')){
      			include(THEME_LOOPS_DIR.'/extraloops.php');
      			foreach($extraloops as $loops){
      				?>
      				<option value="<?php echo $loops["file"];?>" <?php selected($single,$loops["file"]);?>><?php _e($loops["name"], 'ultimatum') ?></option>
      				<?php 
      			}
      		}
      if ( is_plugin_active( 'wonderloops/wonderloops.php' ) ) {
          $theme_loops_dir = @opendir(ULTLOOPBUILDER_DIR);
          $loop_files = array();
          if ($theme_loops_dir) {
              while (($file = readdir($theme_loops_dir)) !== false) {
                  if (substr($file, 0, 1) == '.')
                      continue;
                  if (substr($file, -4) == '.php')
                      $loop_files[] = $file;
              }
          }
          @closedir($theme_loops_dir);

          if ($theme_loops_dir && !empty($loop_files)) {
              foreach ($loop_files as $loop_file) {
                  if (is_readable(ULTLOOPBUILDER_DIR . "/$loop_file")) {
                      unset($data);
                      $data = ultimatum_get_loop_files(ULTLOOPBUILDER_DIR . "/$loop_file");

                      if (isset($data['generator']) && !empty($data['generator'])) {
                          ?>
                          <option
                              value="<?php echo $data["file"]; ?>" <?php selected($single, $data["file"]); ?>><?php _e($data["name"], 'ultimatum') ?></option>
                      <?php
                      }
                  }
              }
          }
      }
      	?>
         <option value="fimage" <?php selected($single,'fimage');?>><?php _e('Full image on Top', 'ultimatum') ?></option>
         <option value="nimage" <?php selected($single,'nimage');?>><?php _e('No image', 'ultimatum') ?></option>
         <option value="limage" <?php selected($single,'limage');?>><?php _e('Image On Left', 'ultimatum') ?></option>
         <option value="rimage" <?php selected($single,'rimage');?>><?php _e('Image On Right', 'ultimatum') ?></option>
      </select>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('noimage'); ?>"><?php _e('No Image', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('noimage'); ?>" id="<?php echo $this->get_field_id('noimage'); ?>">
      <option value="true" <?php selected($noimage,'true');?>>Show Placeholder</option>
      <option value="false" <?php selected($noimage,'false');?>>OFF</option>
      </select>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('singlew'); ?>"><?php _e('Image Width on Single Post', 'ultimatum') ?>:</label>
      <input type="text" value="<?php echo $singlew;?>" name="<?php echo $this->get_field_name('singlew'); ?>" id="<?php echo $this->get_field_id('singlew'); ?>" /><i>Applied on Image on Left/Right Aligned pages</i>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('singleh'); ?>"><?php _e('Image Height on Single Post', 'ultimatum') ?>:</label>
      <input type="text" value="<?php echo $singleh;?>"  name="<?php echo $this->get_field_name('singleh'); ?>" id="<?php echo $this->get_field_id('singleh'); ?>" />
      </p>
       <p>
      <label for="<?php echo $this->get_field_id('show_comments_form'); ?>"><?php _e('Show Comment Form and Comments',THEME_ADMIN_LANG_DOMAIN) ?>:</label>
      <select name="<?php echo $this->get_field_name('show_comments_form'); ?>" id="<?php echo $this->get_field_id('show_comments_form'); ?>">
      <option value="true" <?php selected($show_comments_form,'true');?>>ON</option>
      <option value="false" <?php selected($show_comments_form,'false');?>>OFF</option>
      </select>
      </p>
      <p>
         <label for="<?php echo $this->get_field_id('cats'); ?>"><?php _e('Taxonomy', 'ultimatum') ?>:</label>
         <select name="<?php echo $this->get_field_name('cats'); ?>" id="<?php echo $this->get_field_id('cats'); ?>">
         <option value="ameta" <?php selected($cats,'ameta');?>><?php _e('After Meta', 'ultimatum') ?></option>
         <option value="acontent" <?php selected($cats,'acontent');?>><?php _e('After Content', 'ultimatum') ?></option>
         <option value="false" <?php selected($cats,'false');?>>OFF</option>
         </select>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('meta'); ?>"><?php _e('Meta', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('meta'); ?>" id="<?php echo $this->get_field_id('meta'); ?>">
         <option value="atitle" <?php selected($meta,'atitle');?>><?php _e('After Title', 'ultimatum') ?></option>
         <option value="atext" <?php selected($meta,'atext');?>><?php _e('After Content', 'ultimatum') ?></option>
         <option value="false" <?php selected($meta,'false');?>>OFF</option>
      </select>
      </p>
      <fieldset><legend><?php _e('Single Post Meta Properties', 'ultimatum') ?></legend>
      <p>
         <label for="<?php echo $this->get_field_id('mseperator'); ?>"><?php _e('Meta Seperator', 'ultimatum') ?>:</label>
         <input name="<?php echo $this->get_field_name('mseperator'); ?>" id="<?php echo $this->get_field_id('mseperator'); ?>" value="<?php echo $mseperator; ?>" />
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Date', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('date'); ?>" id="<?php echo $this->get_field_id('date'); ?>">
      <option value="true" <?php selected($date,'true');?>>ON</option>
      <option value="false" <?php selected($date,'false');?>>OFF</option>
      </select>

      <?php  ultimatum_content_inpcheckbox( 'showtime', $showtime, 'Show time', $this); ?>

      <label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Author', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('author'); ?>" id="<?php echo $this->get_field_id('author'); ?>">
      <option value="true" <?php selected($author,'true');?>>ON</option>
      <option value="false" <?php selected($author,'false');?>>OFF</option>
      </select>

      <label for="<?php echo $this->get_field_id('comments'); ?>"><?php _e('Comments', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('comments'); ?>" id="<?php echo $this->get_field_id('comments'); ?>">
      <option value="true" <?php selected($comments,'true');?>>ON</option>
      <option value="false" <?php selected($comments,'false');?>>OFF</option>
      </select>
      </p>
      </fieldset>
      <br />
      <p>
         <label for="<?php echo $this->get_field_id('imgpos'); ?>"><?php _e('Image Position', 'ultimatum') ?>:</label>
         <select name="<?php echo $this->get_field_name('imgpos'); ?>" id="<?php echo $this->get_field_id('imgpos'); ?>">
         <option value="atitle" <?php selected($imgpos,'atitle');?>><?php _e('After Title', 'ultimatum') ?></option>
         <option value="btitle" <?php selected($imgpos,'btitle');?>><?php _e('Before Title', 'ultimatum') ?></option>
         </select>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('gallery'); ?>"><?php _e('Replace Featured Image with gallery or Video', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('gallery'); ?>" id="<?php echo $this->get_field_id('gallery'); ?>">
         <option value="false" <?php selected($gallery,'false');?>>OFF</option>
         <option value="true" <?php selected($gallery,'true');?>>ON</option>
      </select>
      </p>
      </div>
      <div id="<?php echo $tabmulti;?>">
        <p>
      <label for="<?php echo $this->get_field_id('atitle'); ?>"><?php _e('Archive Title', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('atitle'); ?>" id="<?php echo $this->get_field_id('atitle'); ?>">
      <option value="ON" <?php selected($atitle,'ON');?>>ON</option>
      <option value="OFF" <?php selected($atitle,'OFF');?>>OFF</option>
      </select>
      </p>
         <p>
      <label for="<?php echo $this->get_field_id('mtitle'); ?>"><?php _e('Title', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('mtitle'); ?>" id="<?php echo $this->get_field_id('mtitle'); ?>">
      <option value="true" <?php selected($mtitle,'true');?>>Yes With Links</option>
      <option value="nolink" <?php selected($mtitle,'nolink');?>>Yes Without Links</option>
      <option value="false" <?php selected($mtitle,'false');?>>OFF</option>
      </select>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('perpage'); ?>"><?php _e('Items Per Page', 'ultimatum') ?>:</label>
      <input type="text" value="<?php echo $perpage;?>" name="<?php echo $this->get_field_name('perpage'); ?>" id="<?php echo $this->get_field_id('perpage'); ?>" />
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('multiple'); ?>"><?php _e('Layout When Page has Multiple Posts', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('multiple'); ?>" id="<?php echo $this->get_field_id('multiple'); ?>">
       <?php 
      		if(file_exists(THEME_LOOPS_DIR.'/extraloops.php')){
      			include(THEME_LOOPS_DIR.'/extraloops.php');
      			foreach($extraloops as $loops){
      				
      				?>
      				<option value="<?php echo $loops["file"];?>" <?php selected($multiple,$loops["file"]);?>><?php _e($loops["name"], 'ultimatum') ?></option>
      				<?php 
      			}
      		}
      		$theme_loops_dir = @opendir(ULTLOOPBUILDER_DIR);
      		$loop_files = array();
      		if ( $theme_loops_dir ) {
      			while (($file = readdir( $theme_loops_dir ) ) !== false ) {
      				if ( substr($file, 0, 1) == '.' )
      					continue;
      				if ( substr($file, -4) == '.php' )
      					$loop_files[] = $file;
      			}
      		}
      		@closedir( $theme_loops_dir );
      		
      		if ( $theme_loops_dir && !empty($loop_files) ) {
      			foreach ( $loop_files as $loop_file ) {
      				if ( is_readable( ULTLOOPBUILDER_DIR."/$loop_file" ) ) {
      					unset($data);
      					$data = ultimatum_get_loop_files( ULTLOOPBUILDER_DIR."/$loop_file" );
      		
      					if ( isset($data['generator']) && !empty($data['generator']) ) {
      						?>
      		      						<option value="<?php echo $data["file"];?>" <?php selected($multiple,$data["file"]);?>><?php _e($data["name"], 'ultimatum') ?></option>
      		      						<?php 
      		      					}
      		      				}
      		      			}
      		      		}
      	?>
         <option value="1-col-i" <?php selected($multiple,'1-col-i');?>><?php _e('One Column With Full Image', 'ultimatum') ?></option>
         <option value="1-col-li" <?php selected($multiple,'1-col-li');?>><?php _e('One Column With Image On Left', 'ultimatum') ?></option>
         <option value="1-col-ri" <?php selected($multiple,'1-col-ri');?>><?php _e('One Column With Image On Right', 'ultimatum') ?></option>
         <option value="1-col-gl" <?php selected($multiple,'1-col-gl');?>><?php _e('One Column Gallery With Image On Left', 'ultimatum') ?></option>
         <option value="1-col-gr" <?php selected($multiple,'1-col-gr');?>><?php _e('One Column Gallery With Image On Right', 'ultimatum') ?></option>
         <option value="1-col-n" <?php selected($multiple,'1-col-n');?>><?php _e('One Column With No Image', 'ultimatum') ?></option>
         <option value="2-col-i" <?php selected($multiple,'2-col-i');?>><?php _e('Two Columns With Image', 'ultimatum') ?></option>
         <option value="2-col-g" <?php selected($multiple,'2-col-g');?>><?php _e('Two Columns Gallery', 'ultimatum') ?></option>
         <option value="2-col-n" <?php selected($multiple,'2-col-n');?>><?php _e('Two Columns With No Image', 'ultimatum') ?></option>
         <option value="3-col-i" <?php selected($multiple,'3-col-i');?>><?php _e('Three Columns With Image', 'ultimatum') ?></option>
         <option value="3-col-g" <?php selected($multiple,'3-col-g');?>><?php _e('Three Columns Gallery', 'ultimatum') ?></option>
         <option value="3-col-n" <?php selected($multiple,'3-col-n');?>><?php _e('Three Columns With No Image', 'ultimatum') ?></option>
         <option value="4-col-i" <?php selected($multiple,'4-col-i');?>><?php _e('Four Columns With Image', 'ultimatum') ?></option>
         <option value="4-col-g" <?php selected($multiple,'4-col-g');?>><?php _e('Four Columns Gallery', 'ultimatum') ?></option>
         <option value="4-col-n" <?php selected($multiple,'4-col-n');?>><?php _e('Four Columns With No Image', 'ultimatum') ?></option>
      </select>
      </p>
         <p>
      <label for="<?php echo $this->get_field_id('mnoimage'); ?>"><?php _e('No Image', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('mnoimage'); ?>" id="<?php echo $this->get_field_id('mnoimage'); ?>">
      <option value="true" <?php selected($mnoimage,'true');?>>Show Placeholder</option>
      <option value="false" <?php selected($mnoimage,'false');?>>OFF</option>
      </select>
      </p>
       <p>
         <label for="<?php echo $this->get_field_id('mimgpos'); ?>"><?php _e('Image Position', 'ultimatum') ?>:</label> 
         <select name="<?php echo $this->get_field_name('mimgpos'); ?>" id="<?php echo $this->get_field_id('mimgpos'); ?>">
         <option value="atitle" <?php selected($mimgpos,'atitle');?>><?php _e('After Title', 'ultimatum') ?></option>
         <option value="btitle" <?php selected($mimgpos,'btitle');?>><?php _e('Before Title', 'ultimatum') ?></option>


         </select>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('mvideo'); ?>"><?php _e('Replace Featured Image with gallery or Video', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('mvideo'); ?>" id="<?php echo $this->get_field_id('mvideo'); ?>">
         <option value="false" <?php selected($mvideo,'false');?>>OFF</option>
         <option value="true" <?php selected($mvideo,'true');?>>ON</option>
      </select>
      </p>
      
      <p>
      <label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Show Content As', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('excerpt'); ?>" id="<?php echo $this->get_field_id('excerpt'); ?>">
      <option value="true" <?php selected($excerpt,'true');?>>Excerpt</option>
      <option value="content" <?php selected($excerpt,'content');?>>Content</option>
      <option value="false" <?php selected($excerpt,'false');?>>OFF</option>
      </select>
      </p>
       <p>
      <label for="<?php echo $this->get_field_id('excerptlength'); ?>"><?php _e('Excerpt Length(words)', 'ultimatum') ?>:</label>
      <input type="text" value="<?php echo $excerptlength;?>" name="<?php echo $this->get_field_name('excerptlength'); ?>" id="<?php echo $this->get_field_id('excerptlength'); ?>" />
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('multiplew'); ?>"><?php _e('Image Width', 'ultimatum') ?>:</label>
      <input type="text" value="<?php echo $multiplew;?>" name="<?php echo $this->get_field_name('multiplew'); ?>" id="<?php echo $this->get_field_id('multiplew'); ?>" /><i>Applied on Image on Left/Right Aligned pages</i>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('multipleh'); ?>"><?php _e('Image Height', 'ultimatum') ?>:</label>
      <input type="text" value="<?php echo $multipleh;?>" name="<?php echo $this->get_field_name('multipleh'); ?>" id="<?php echo $this->get_field_id('multipleh'); ?>" />
      </p>
      <p>
         <label for="<?php echo $this->get_field_id('mcats'); ?>"><?php _e('Taxonomy', 'ultimatum') ?>:</label>
         <select name="<?php echo $this->get_field_name('mcats'); ?>" id="<?php echo $this->get_field_id('mcats'); ?>">
         <option value="ameta" <?php selected($mcats,'ameta');?>><?php _e('After Meta', 'ultimatum') ?></option>
         <option value="acontent" <?php selected($mcats,'acontent');?>><?php _e('After Content', 'ultimatum') ?></option>
         <option value="false" <?php selected($mcats,'false');?>>OFF</option>
         </select>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('mmeta'); ?>"><?php _e('Meta', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('mmeta'); ?>" id="<?php echo $this->get_field_id('mmeta'); ?>">
      <option value="atitle" <?php selected($mmeta,'atitle');?>><?php _e('After Title', 'ultimatum') ?></option>
      <option value="atext" <?php selected($mmeta,'atext');?>><?php _e('After Content', 'ultimatum') ?></option>
      <option value="false" <?php selected($mmeta,'false');?>>OFF</option>
      </select>
      </p>
      <fieldset><legend>Multi Post Meta Properties</legend>
      <p>
         <label for="<?php echo $this->get_field_id('mmseperator'); ?>"><?php _e('Meta Seperator', 'ultimatum') ?>:</label>
         <input name="<?php echo $this->get_field_name('mmseperator'); ?>" id="<?php echo $this->get_field_id('mmseperator'); ?>" value="<?php echo $mmseperator; ?>" />
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('mdate'); ?>"><?php _e('Date', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('mdate'); ?>" id="<?php echo $this->get_field_id('mdate'); ?>">
      <option value="true" <?php selected($mdate,'true');?>>ON</option>
      <option value="false" <?php selected($mdate,'false');?>>OFF</option>
      </select>
      <?php  ultimatum_content_inpcheckbox( 'mshowtime', $mshowtime, 'Show time', $this); ?>

      <label for="<?php echo $this->get_field_id('mauthor'); ?>"><?php _e('Author', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('mauthor'); ?>" id="<?php echo $this->get_field_id('mauthor'); ?>">
      <option value="true" <?php selected($mauthor,'true');?>>ON</option>
      <option value="false" <?php selected($mauthor,'false');?>>OFF</option>
      </select>

      <label for="<?php echo $this->get_field_id('mcomments'); ?>"><?php _e('Comments', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('mcomments'); ?>" id="<?php echo $this->get_field_id('mcomments'); ?>">
      <option value="true" <?php selected($mcomments,'true');?>>ON</option>
      <option value="false" <?php selected($mcomments,'false');?>>OFF</option>
      </select>
      </p></fieldset>
      <p>
      <label for="<?php echo $this->get_field_id('mreadmore'); ?>"><?php _e('Read More Link', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('mreadmore'); ?>" id="<?php echo $this->get_field_id('mreadmore'); ?>">
      <option value="after" <?php selected($mreadmore,'after');?>><?php _e('Right after excerpt','ultimatum') ?></option>
      <option value="right" <?php selected($mreadmore,'right');?>><?php _e('Right Aligned', 'ultimatum') ?></option>
      <option value="left" <?php selected($mreadmore,'left');?>><?php _e('Left Aligned', 'ultimatum') ?></option>
      <option value="false" <?php selected($mreadmore,'false');?>>OFF</option>
      </select>
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('rmtext'); ?>"><?php _e('Read More Text:', 'ultimatum') ?></label>
      <select name="<?php echo $this->get_field_name('rmtext'); ?>" id="<?php echo $this->get_field_id('rmtext'); ?>">
      <option value="Read More" <?php selected($rmtext,'Read More');?>><?php _e('Read More','ultimatum') ?></option>
      <option value="More" <?php selected($rmtext,'More');?>><?php _e('More','ultimatum') ?></option>
      <option value="Continue Reading" <?php selected($rmtext,'Continue Reading');?>><?php _e('Continue Reading','ultimatum') ?></option>
      <option value="Continue" <?php selected($rmtext,'Continue');?>><?php _e('Continue','ultimatum') ?></option>
      <option value="Details" <?php selected($rmtext,'Details');?>><?php _e('Details','ultimatum') ?></option>
      </select>
        </p>
       <p>
      <label for="<?php echo $this->get_field_id('navigation'); ?>"><?php _e('Navigation', 'ultimatum') ?>:</label>
      <select name="<?php echo $this->get_field_name('navigation'); ?>" id="<?php echo $this->get_field_id('navigation'); ?>">
      <option value="numeric" <?php selected($navigation,'numeric');?>><?php _e('Numeric','ultimatum') ?></option>
      <option value="prenext" <?php selected($navigation,'prenext');?>><?php _e('Prev-Next','ultimatum') ?></option>
	<option value="oldnew" <?php selected($navigation,'oldnew');?>><?php _e('Older-Newer','ultimatum') ?></option>
      </select>
      </p>
     
      </div>
      </div>
      <?php
}

}
add_action('widgets_init', create_function('', 'return register_widget("UltimatumContent");'));


function ultimatum_content_inpcheckbox( $fieldid, &$currval, $title, &$that){
// ech( $fieldid, $currval);
?>

      <label for="<?php echo $that->get_field_id($fieldid); ?>"><?php _e($title); ?></label>
      <input id="<?php echo $that->get_field_id($fieldid); ?>" name="<?php echo $that->get_field_name($fieldid); ?>" type="checkbox" value="1"  <?php checked($currval, 1, true); ?> />

<?php
} // end ultimatum_inpcheckbox


function ultimatum_content_inptextarea( $fieldid, &$currval, $title, &$that, $rows = '', $cols =''){

   $format ='';

   if ($rows !== '' ){  $format = ' rows="' .$rows. '" ';  }
   if ($cols !== '' ){  $format .= ' cols="' .$cols. '" ';  }
?>

      <label for="<?php echo $that->get_field_id($fieldid); ?>"><?php _e($title, 'ultimatum') ?>:</label>

      <textarea name="<?php echo $that->get_field_name($fieldid); ?>" id="<?php echo $that->get_field_id($fieldid); ?>" <?php echo $format; ?> ><?php echo $currval; ?></textarea>

<?php

}
?>