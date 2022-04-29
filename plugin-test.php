<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once(plugin_dir_path( __FILE__ ) . '/dataset.php');


class WPPlayers
{
    
    private $playerLimiter;
    private $dataset;
    const DEFAULT_PROMPT = "Please , Insert Name Of PLAYERS.";

    public function __construct()
    {
        
        add_shortcode('players_list', array($this, 'shortcode'));
    }


    public function shortcode($atts, $content)
    {

        if (empty($content)) {
            $content = WPPlayers::DEFAULT_PROMPT;
        }

        $args = $this->processShortcodeAtts($atts);
        $this->initialise($args);

        ob_start();

        ?>
        <form action="#v_form" method="post" id="v_form">
            <label for="player_target"><h2><?php echo $content ?></h2></label>
            <input type="text" name="player_target" id="player_target"/>
            <input type="submit" name="submit_form" value="submit"/>
        </form>
        <?php

        $html = ob_get_clean();


        if (isset($_POST["submit_form"])) {
            $validationError = $this->handleFormSubmission();

            if (empty($validationError)) {
          
                $html .= '<p style="color:green"> submitted successfully</p>';
            } else {

                $html .= '<p style="color:red"> submission failed: ' . $validationError . '</p>';
            }
        }


        $data = $this->dataset->select($this->playerLimiter);
        $html .= $this->presentData($data);

        return $html;
    }



    private function initialise($args) {
        $this->playerLimiter = $this->sanitise($args['player_limiter']);

        $this->dataset = new WPPlayerlist($this->sanitise($args['table_name']));
        $this->dataset->initialise();
    }


    private function processShortcodeAtts($atts) {
        $args = shortcode_atts(
            array(
                'table_name' => 'players_table',
                'player_limiter' => 4,
            ), $atts);

        return $args;
    }



    private function sanitise($value)
    {
        return strip_tags($value, "");
    }



    private function handleFormSubmission()
    {
        $validationError = $this->validateForm();

        if (!empty($validationError)) {
            return $validationError;
        }

        $sanitisedPainTarget = $this->sanitise($_POST["player_target"]);
        if ($this->dataset->insert($sanitisedPainTarget) === false) {
            return '<span class="baaad">Database insert failed</span>';
        }

        return null;
    }



    private function validateForm()
    {
        if ($_POST["player_target"] == "") {
            return "I need player name";
        }
        return null;
    }


    private function presentData($rows)
    {
        $htmlTable = "<table><thead><th>ID</th><th>List of players</th> </thead><tbody>";
        foreach ($rows as $k => $v) {
            $htmlTable .= "<tr><td>$v->id</td><td>$v->list_player</td></tr>";
        }

        return $htmlTable . "</tbody></table>";
    }
}

$WPPlayers = new WPPlayers();

