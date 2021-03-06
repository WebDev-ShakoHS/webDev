<?php
error_reporting(E_ALL);  // Turn on all errors, warnings and notices for easier debugging

// API request variables
$endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
$version = '1.0.0';  // API version supported by your application
$appid = 'RobertMa-Shakopee-PRD-169ec6b8e-bb30ba02';  // Replace with your own AppID
$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
$query = ' Abraham lincoln';  // You may want to supply your own query
$safequery = urlencode($query);  // Make the query URL-friendly
$i = '0';  // Initialize the item filter index to 0
// Create a PHP array of the item filters you want to use in your request
$filterarray =
  array(
    array(
      'name' => 'MaxPrice',
      'value' => '25',
      'paramName' => 'Currency',
      'paramValue' => 'USD'
    ),
    array(
      'name' => '',
      'value' => 'true',
      'paramName' => '',
      'paramValue' => ''
    ),
    array(
      'name' => 'ListingType',
      'value' => array('AuctionWithBIN', 'FixedPrice'),
      'paramName' => '',
      'paramValue' => ''
    ),
  );

// Generates an indexed URL snippet from the array of item filters
function buildURLArray($filterarray)
{
  global $urlfilter;
  global $i;
  // Iterate through each filter in the array
  foreach ($filterarray as $itemfilter) {
    // Iterate through each key in the filter
    foreach ($itemfilter as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $j => $content) { // Index the key for each value
          $urlfilter .= "&itemFilter($i).$key($j)=$content";
        }
      } else {
        if ($value != "") {
          $urlfilter .= "&itemFilter($i).$key=$value";
        }
      }
    }
    $i++;
  }
  return "$urlfilter";
} // End of buildURLArray function

// Build the indexed item filter URL snippet
buildURLArray($filterarray);


// Construct the findItemsByKeywords HTTP GET call
$apicall = "$endpoint?";
$apicall .= "OPERATION-NAME=findItemsByKeywords";
$apicall .= "&SERVICE-VERSION=$version";
$apicall .= "&SECURITY-APPNAME=$appid";
$apicall .= "&GLOBAL-ID=$globalid";
$apicall .= "&keywords=$safequery";
$apicall .= "&paginationInput.entriesPerPage=50";
$apicall .= "$urlfilter";
// Load the call and capture the document returned by eBay API
$resp = simplexml_load_file($apicall);

// Check to see if the request was successful, else print an error
if ($resp->ack == "Success") {
  $results = '';
  // If the response was loaded, parse it and build links
  foreach ($resp->searchResult->item as $item) {
    $pic   = $item->galleryURL;
    $link  = $item->viewItemURL;
    $title = $item->title;
    /////////////////////////EDIT THIS LINE/////////////////////////////////////////////////////
    // For each SearchResultItem node, build a link and append it to $results
    $results .= "<tr><td><img src=\"$pic\"></td><td><a href=\"$link\">$title</a></td></tr>";
    ////////////////////////EDIT THIS LINE//////////////////////////////////////////////////////      
  }
}
// If the response does not indicate 'Success,' print an error
else {
  $results  = "<h3>Oops! The request was not successful. Make sure you are using a valid ";
  $results .= "AppID for the Production environment.</h3>";
}
?>
<!-- Build the HTML page with values from the call response -->
<html>

<head>
  <!-----css---->
  <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
  <!-- Meta -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="SHS WebDev Version 3.0">

  <title>Results for <?php echo $query; ?></title>
  <style type="text/css">
    body {
      font-family: arial, sans-serif;
      background-color: coral;
    }

    .image {
      display: block;
      width: 100%;
      height: auto;
      display: list-item;
    }

    .overlay {
      position: sticky;
      bottom: 0;
      background: rgb(0, 0, 0);
      background: rgba(0, 0, 0, 0.5);
      /* Black see-through */
      color: #f1f1f1;
      width: 100%;
      transition: .5s ease;
      opacity: 0;
      color: white;
      font-size: 20px;
      padding: 20px;
      text-align: inherit;
    }

    * {
      box-sizing: border-box;
    }
  </style>
</head>

<body>

  <center>
    <h1>Search Results for<?php echo $query; ?></h1>
  </center>
  <table>
    <tr>
      <center> <?php echo $results; ?></center>
    <tr>
    </tr>
  </table>

</body>


</html>