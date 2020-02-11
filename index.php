<?php

/******************************************************************************
 * Project        VOTING PLATFORM
 *****************************************************************************/
/**
 * @author        Franz Lorenz
 */

/*----------------------------------------------------------
 *  INCLUDES
 *--------------------------------------------------------*/

/*----------------------------------------------------------
 *  DEFINITIONS
 *--------------------------------------------------------*/
/**
 * A storage class for a person.
 */
class Person
{
   public $sName = "";
   public $aChoice = [];
}

/*----------------------------------------------------------
 *  GLOBALS
 *--------------------------------------------------------*/

/**
 * This global variable contains the filename of the CSV file
 */
$gsCsvFile = "";              

/**
 * This global variable contains the name of the selected person.
 */
$gsPerson  = "";

/**
 * This global variable contains the id of the current voting
 */
$gsId = "";

/**
 * This global array contains all available choices.
 */
$aChoices = array();

/**
 * This global variable contains an array of the classes
 * 'Person' with all the available persons.
 */
$aPersons = array();


/*----------------------------------------------------------
 *  FUNCTIONS
 *--------------------------------------------------------*/

/**
 * This function reads all required data from the
 * CSV file. The data will be filled to the global
 * variables $aChoices and $aPersons.
 * @param   sCsvFile    csv filename
 */
function csv_getRawData( $sCsvFile ) 
{
   global $aChoices;
   global $aPersons;
   //
   $aChoices = array();                                     //clear the previous choices
   $aPersons = array();                                     //clear the previous persons
   //
   //
   // read the available choices
   $aCsv = explode( "\n", file_get_contents( $sCsvFile ) ); //get the csv content
   $aItems = explode( ";", $aCsv[0] );                      //split content of the first line
   for( $nItem=1; $nItem < count( $aItems ); $nItem++ )     //step through all items of the first line
   {                                                        // then...
      $aChoices[] = trim( $aItems[$nItem] );                // add each item to the list-of-choices
   }  //for()
   //
   // read the possible persons
   for( $nItem=1; $nItem < count( $aCsv ); $nItem++ )       //step through the second, third... lines
   {                                                        // then...
      $aItems  = explode( ";", $aCsv[$nItem] );             // split content of the current line
      $sPerson = trim( $aItems[0] );                        // get the first item of current line as persons name
      if( strlen( $sPerson ) > 0 )                          // valid name?
      {                                                     //  yes, then...
         $aNew = new Person();                              //  create a new class element for the person
         $aNew->sName = $sPerson;                           //  set the name of the person
         $aNew->aChoice = array_slice( $aItems, 1 );        //  set the choices
         $aPersons[] = $aNew;                               //  add the new class element to the global person list
      }
   }  //for()
}

/**
 * This function will write the CSV file with the
 * updated data.
 * @param   sCsvFile    csv filename
 */
function csv_writeRawData( $sCsvFile )
{
   global $aChoices;
   global $aPersons;
   //
   $hCsv = fopen( $sCsvFile, "wt" );                        //open file for writing
   fprintf( $hCsv, ";" );                                   //write first line
   fprintf( $hCsv, "%s\n", implode( ";", $aChoices ) );     // with all choices
   foreach( $aPersons as $sPerson )                         //write each line with the persons
   {                                                        // then...
      fprintf( $hCsv, "%s", $sPerson->sName );              // write the person name
      foreach( $sPerson->aChoice as $sChoice )
      {
         fprintf( $hCsv, ";%d", $sChoice );
      }
      fprintf( $hCsv, "\n" );
   }
   fclose( $hCsv );
}

/**
 * This function returns the filename of the csv file.
 * @param   $sCsvFile   filename with extension
 * @return  string      description
 */
function getName( $sCsvFile )
{
   $aName = explode( "_", $sCsvFile );
   $aName = explode( ".", implode( "_", array_slice( $aName, 1 ) ) );
   return $aName[0];
}

/*----------------------------------------------------------
 *  MAIN
 *--------------------------------------------------------*/

include( "head.html" );

//
// get the query parameter
parse_str( $_SERVER['QUERY_STRING'], $aQuery );
$nQueryLen = count( $aQuery );

//
// check parameters...
// person=Lorenz%2C+Franz
// id=0001
if( ! empty( $aQuery['id'] ) )                              //is parameter id=xxx given?
{                                                           // yes, then...
   $gsId = $aQuery['id'];                                   // store the current ID
   $aFiles = glob( $gsId."_*.csv" );                        // search for a csv file
   if( count( $aFiles ) == 1 )                              // is ONE csv file given?
   {                                                        //  yes, then...
      $gsCsvFile = $aFiles[0];                              //  get the real filename
   }
   $nQueryLen--;                                            // decrement number of remaining parameters
}
//
if( ! empty( $aQuery['person'] ) )                          //is parameter person=xxx given?
{                                                           // yes, then...
   $gsPerson = $aQuery['person'];                           // get the parameter value
   $nQueryLen--;                                            // decrement number of remaining parameters
}

//
// check, if the voting setup file is valid
if( strlen( $gsCsvFile ) == 0 )
{
   echo "<div class='w3-container w3-teal'>\n";
   echo "  <h1>Voting Platform</h1>\n";
   echo "</div>\n";
   echo "<div class='w3-panel w3-pale-red w3-leftbar w3-rightbar w3-border-red'>\n";
   echo "<p>The voting 'id' is not valid!<br/>Please contact the system administrator to get a valid 'id'.</p>\n";
   echo "</div>\n";
}
else if( $gsPerson == "admin" )
{
   csv_getRawData( $gsCsvFile );                            // get data from csv file
   echo "<div class='w3-container w3-teal'>\n";
   echo "  <h1>Voting of '".getName( $gsCsvFile )."'</h1>\n";
   echo "</div>\n";
   echo "<br/>\n";
   echo "<div class='w3-card-4' w3-margin>\n";
   echo "  <table class='w3-table-all'>\n";
   echo "    <tr class='w3-red'><th>Name</th><th>Value</th></tr>\n";
   echo "    <tr><td>CSV Filename</td><td>".$gsCsvFile."</td></tr>\n";
   $sSrvLink = explode( "?", $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] )[0];
   if( strpos( $sSrvLink, "http" ) == 0 )
   {
      $sSrvLink = "http://".$sSrvLink;
   }
   foreach( $aPersons as $sPerson )                         // search all available persons
   {                                                        //  then...
      $sLink = $sSrvLink."?id=".$gsId."&person=".urlencode( $sPerson->sName );
      echo "<tr><td>User Link</td><td><a href='".$sLink."'>".$sLink."</a></td></tr>\n";
   }
   echo "  </table>\n";
   echo "</div>\n";
}
else if( ( strlen( $gsPerson ) > 0 ) && ( $nQueryLen > 0 ) )
{
   // $gsPerson is set and parameter set
   // person=Lorenz%2C+Franz&1=on&2=on&3=on&4=on&5=on&6=on&8=on
   //
   csv_getRawData( $gsCsvFile );                            // get data from csv file
   foreach( $aPersons as $sPerson )                         // search all available persons
   {                                                        //  then...
      if( $sPerson->sName == $gsPerson )                    //  is person found?
      {                                                     //   yes, then...
         for( $nChoice=0; $nChoice < count( $sPerson->aChoice ); $nChoice++ )
         {
            if( empty( $aQuery[$nChoice] ) )
            {
               $sPerson->aChoice[$nChoice] = 0;
            }
            else
            {
               $sPerson->aChoice[$nChoice] = 1;
            }
         }  //for( $nChoice... )
         break;
      }  //if( $sPerson->sName == $gsPerson )
   }  //foreach()
   csv_writeRawData( $gsCsvFile );
   //
   echo "<script>\n";
   echo "alert( 'Thank you for the voting!' );\n";
   echo "this.document.location.href = 'index.php?id=";
   echo $aQuery['id'];
   echo "';\n";
   echo "</script>\n";
}
//
// handle only viewing
else if( ( strlen( $gsCsvFile ) > 0 ) && ( strlen( $gsPerson ) >= 0 ) )
{
   $nSaveButtonVisible = 0;
   csv_getRawData( $gsCsvFile );                            // get data from csv file
   echo "<div class='w3-container w3-teal'>\n";
   echo "  <h1>Voting of '".getName( $gsCsvFile )."'</h1>\n";
   echo "</div>\n";
   //
   echo "<div class='w3-card-4'>\n";
   echo "  <form action='index.php'>\n";
   echo "  <input type='hidden' name='id' value='".$gsId."'>\n";
   echo "  <input type='hidden' name='person' value='".$gsPerson."'>\n";
   echo "  <br/>\n";
   echo "  <table class='w3-table-all w3-hoverable w3-margin w3-padding'>\n";
   echo "    <thead>\n";
   echo "      <tr class='w3-light-grey w3-small'>\n";
   echo "        <th>Name</th>\n";
   foreach( $aChoices as $sChoice )
   {
      echo "        <th>".$sChoice."</th>\n";
   }
   echo "      </tr>\n";
   echo "    </thead>\n";
   //
   $nChoices = count( $aChoices );
   foreach( $aPersons as $sPerson )
   {
      echo "      <tr>\n";
      if( $sPerson->sName == $gsPerson )
      {
         $nSaveButtonVisible = 1;
         echo "        <td class='w3-cell-middle w3-teal'>".$sPerson->sName."</td>\n";
         for( $nChoice=0; $nChoice < $nChoices; $nChoice++ )
         {
            if( $sPerson->aChoice[$nChoice] > 0 )
            {
               $sChecked = "checked='checked'";
            }
            else
            {
               $sChecked = "";
            }
            echo "        <td><input name='".$nChoice."' class='w3-check' type='checkbox' ".$sChecked."></td>\n";
         }
      }
      else
      {
         echo "        <td class='w3-cell-middle'>".$sPerson->sName."</td>\n";
         for( $nChoice=0; $nChoice < $nChoices; $nChoice++ )
         {
            if( $sPerson->aChoice[$nChoice] > 0 )
            {
               $sChecked = "checked='checked'";
            }
            else
            {
               $sChecked = "";
            }
            echo "        <td><input class='w3-check' type='checkbox' disabled ".$sChecked."></td>\n";
         }
      }
      echo "      </tr>\n";
   }
   echo "  </table>\n";
   echo "  <br/>\n";
   if( $nSaveButtonVisible != 0 )                           // should the save button be visible?
   {                                                        //  yes, then...
      echo "  <button type='submit' class='w3-button w3-panel w3-green w3-margin w3-padding-large'>Save</button>\n";
   }
   echo "  <br/>\n";
   echo "  </form>\n";
   echo "</div>\n";
}

include( "tail.html" );

?>
