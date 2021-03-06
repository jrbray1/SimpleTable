<?php
 
/*
 * Tabbed Data extension.
 *
 * This extension implements a simple method to display tabular data, with a
 * far simpler markup than the standard Wiki table markup.  The goal is to
 * allow tabular data to be very easily pasted in from an external source,
 * such as a spreadsheet.  The disadvantage is that it doesn't allow for any
 * fancy formatting; for example, there is no way (as yet) to set row and cell
 * parameters, including row and cell spanning.  However, it makes a very
 * simple way to include tabular data in a Wiki.
 *
 * All you need to do is prepare your data in rows, with fields separated
 * by tab characters.  Excel's "Save as" -> "Text (Tab delimited)" function
 * saves data in this format.  Place the data inside <tab>...</tab> tags,
 * and set any table parameters inside the opening <tab> tag; eg.:
 *
 *   <tab class=wikitable>
 *   field1\tfield2\t...
 *   field1\tfield2\t...
 *   </tab>
 *
 * Additional parameters allowed in the tag are:
 *   sep                  Specify a different separator; see the $separators array.
 *   head                 Specify a heading; "head=top" makes the first row a heading,
 *                        "head=left" makes the first column a heading, "head=topleft"
 *                        does both.
 *   applycssborderstyle  Adds a style to the table and each cell using css
 *                        border-colapse to give the table a black 1px border
 *
 * 1.2a last version by JohanTheGhost
 * 1.3 add barbar separator and allow collapsing. John Bray
 * 2.0 rewrite registration procedure. John Bray
 *
 * Thanks for contributions to:
 *	Smcnaught
 *	Frederik Dohr
 */

class SimpleTable {


    // Register any tab callbacks with the parser
    public static function onParserFirstCallInit( Parser $parser ) {
        // When the parser sees the <tav> tag, it executes hookTab (see below)
        $parser->setHook( 'tab', [ self::class, 'hookTab' ] );
    }

    /*
     * The hook function. Handles <tab></tab>.
     * Receives the table content and <tab> parameters.
     */
    public static function hookTab( $tableText, array $args, Parser $parser, PPFrame $frame ) {

      /*
       * The permitted separators.  An array of separator style name
       * and preg pattern to match it.
       */
      $separators = array(
        'space' => '/ /',
        'spaces' => '/\s+/',
        'tab' => '/\t/',
        'comma' => '/,/',
        'colon' => '/:/',
        'semicolon' => '/;/',
        'bar' => '/\|/',
        'barbar' => '/\|\|`/',
      );

        // The default field separator.
        $sep = 'barbar';
 
        // Default value for using table headings.
        $head = null;

        // If we are to apply border-colapse CSS Border Style
        $applycssborderstyle = false;
 
        // Build the table parameters string from the tag parameters.
        // The 'sep' and 'head' parameters are special, and are handled
        // here, not passed to the table.
	$params = 'data-expandtext="+" data-collapsetext="-"';
	$collapse = '';

        foreach ($args as $key => $val) {
            if ($key == 'sep')
                $sep = $val;
            else if ($key == 'head')
                $head = $val;
            else if ($key == 'applycssborderstyle') 
                $applycssborderstyle = $val;
            else if ($key == 'collapse')
		$collapse= 'mw-collapsed';
            else
                $params .= ' ' . $key . '="' . $val . '"';
        }

        $params .= ' ' . 'class="wikitable mw-collapsible ' . $collapse . '"';
 
        if (!array_key_exists($sep, $separators))
            return "Invalid separator: $sep";
 
        // Parse and convert the table body.
        $pattern=$separators[$sep];

        $wikitab = '';

        // Remove initial and final newlines.
        $tableText = trim($tableText);

        // Split the input into lines, and convert each line to table format.
        $lines = preg_split('/\n/', $tableText);
        $row = 0;
        foreach ($lines as $line) {
            $wikitab .= "|-\n";
            $bar = strpos($head, 'top') !== false && $row == 0 ? '!' : '|';

            if ($applycssborderstyle !== false) {
              $bar = $bar . 'style="border-style: solid; border-width: 1px" |';
            }

            $fields = preg_split($pattern, $line);
            $col = 0;
            foreach ($fields as $field) {
                $cbar = strpos($head, 'left') !== false && $col == 0 ? '!' : $bar;
                if ($col < sizeof($fields)-1) {
                  /* don't wrap for all but last column */
                  $wikitab .= $cbar . ' <span style="white-space: nowrap;">' . $field . "</span>\n";
                } else {
                  $wikitab .= $cbar . " " . $field . "\n";
                }
                ++$col;
            }
            ++$row;
        }
 
        // If we are not to apply the css border style
        if ($applycssborderstyle == false) {
          // Wrap the body in table tags, with the table parameters.
          $wikiTable = "{|" . $params . "\n" . $wikitab . "|}";
        } else {
          $tablestyletext = 'style="border-collapse: collapse; border-width: 1px; border-style: solid; border-color: #000"';
          $wikiTable = "{|" . $tablestyletext . " " . $params . "\n" . $wikiitab . "|}";
        }
        // Done.  Parse the result, so that the table can contain Wiki
        // text.  Thanks to Smcnaught.
        $ret = $parser->parse($wikiTable,
                              $parser->mTitle,
                              $parser->mOptions,
                              false,
                              false);
	$HTML = trim( str_replace("</table>\n\n", "</table>", $ret->getText()) ); # remove superfluous newlines
        return $HTML;
    }
}
