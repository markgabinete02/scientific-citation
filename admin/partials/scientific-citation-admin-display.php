<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       mark-gabinete
 * @since      1.0.0
 *
 * @package    Scientific_Citation
 * @subpackage Scientific_Citation/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="container" style="max-width: 100%">
    <div class="row">
        <h1>Scientific Citation Settings</h1>
        <hr>

        <div>
            <h2>Settings</h2>
            <form method="post" action="options.php">
                <?php
                    settings_fields("scicitationSettings");
                    do_settings_sections("scicitationSettings");
                ?>

                <div class="form-group">

                    <?php
                        $selected_style = get_option('citation_style');
                    ?>
                    <label for="citatiton_style">Choose Scientific Style Citation:</label>
                    <select class="form-control" id="citation_style" name="citation_style">
                        <option <?php if($selected_style == "apa"){ echo "selected"; } ?> value="apa">APA (American Psychological Association) Citation Style</option>
                        <option <?php if($selected_style == "chicago"){ echo "selected"; } ?> value="chicago">Chicago/Turabian Citation Style</option>
                    </select>
                    
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h2>Shortcode lists</h2>
            <ul>
                <li>1. Journal <code>[citation type="Journal" authors="" article="" journal-title="" volume="" issueno="" publication-year="" pages=""]Text to be cited[/citation]</code></li>
                <li>2. Books <code>[citation type="Book" authors="" book-title="" edition="" place-of-publication="" publisher="" publication-year="" ]Text to be cited[/citation]</code></li>
                <li>3. Magazine <code>[citation type="Magazine" authors="" publication-date="" article="" magazine-title="" volume="" issueno="" pages="" ]Text to be cited[/citation]</code></li>
                <li>4. Website <code>[citation type="Website" author="" publisher="" webpage-title="" date-of-access="" url="" publication-date="" format=""]Text to be cited[/citation]</code></li>
            </ul>

            <h3>Notes:</h3>
            <ul>
                <li> - Multiple authors should be separated by "|" example authors="Langner, M. | Imbach, R." .</li>
                <li> - Format Authors accordingly. Depending on the Citation style your using.</li>
                <li> - This Plugin is limited to accepting multiple authors but not responsible for the Author name's format (might include in future updates).</li>
                <li> - You can refer to this guide <a href="https://www.bibguru.com/guides/">Citation Guides</a> for citation formats.</li>
            </ul>
        </div>
    </div>
    
</div>
