<?php

// Updates to the "current" version of Zenodo: This code will generate a Manifest, but at the time of uploading the manifest will not resolve due to issues with the info.json files produced by Zenodo
if (!$_GET["id"] or $_GET["id"] == "index.html" )
	{$id = "1434056";}
else
	{$id = $_GET["id"];}

$json = "https://zenodo.org/records/$id/export/json";
$files = "https://zenodo.org/api/records/$id/files";
$dets = getRemoteJsonDetails($json, false, 1);
$dets["files"]["json"] = getRemoteJsonDetails($files, false, 1);

$first = current($dets["files"]["json"]["entries"]);

$arr = array(
	"id" => $dets["links"]["doi"],
	"thumb" => $first["links"]["iiif_base"]."/full/,250/0/default.png",
	"description" => json_encode($dets["metadata"]["description"]),
	"title" => json_encode($dets["metadata"]["title"]),	
	"type" => $dets["metadata"]["resource_type"]["id"]
	);
	
if (isset($dets["metadata"]["notes"]))
  {$arr["notes"] = json_encode($dets["metadata"]["notes"]);}
else
  {$arr["notes"] = "";}
  
if (isset($dets["metadata"]["rights"]["id"]))
  {$cr = current ($dets["metadata"]["rights"]["id"]);
   $arr["license"] = $cr["id"];}
else
  {$arr["license"] = "";}
   
if (isset($dets["metadata"]["creators"]))
  {$arr["creators"] = array();
   foreach ($dets["metadata"]["creators"] as $k => $v)
      {$arr["creators"][] = $v["person_or_org"]["name"] . " " . $v["person_or_org"]["family_name"];}   
   }

$arr["info"] = array();
foreach ($dets["files"]["json"]["entries"] as $k => $v)
  {
  $cinfo = getRemoteJsonDetails($v["links"]["iiif_info"], false, 1);
  $cinfo["iiif_info"] = $v["links"]["iiif_info"];
  $arr["info"][] = $cinfo;}

$man = buildManifest($arr);

if ($man)
	{header('Content-Type: application/json');
	 echo $man;}
else
	{http_response_code(403);}

function buildManifest($arr)
	{
	$attrib = "";
	$attrib = json_encode(trim(implode(",", $arr["creators"]), ","));
	
ob_start();	
	echo <<<END
{  
   "@context":"http://iiif.io/api/presentation/2/context.json",
   "@id":"$arr[id]",
   "@type":"sc:Manifest",
   "label":$arr[title],
   "license":"$arr[license]", 
   "attribution":$attrib,
   "logo": "https://about.zenodo.org/static/img/logos/zenodo-gradient-200.png",
   "metadata":[  
      {  
         "label":"Image Description",
         "value":$arr[description]
      }
   ],
   "description":$arr[notes],
   "viewingDirection":"left-to-right",
   "viewingHint":"individuals",
   "sequences":[  
      {  
         "@id":"/zenodo/manifests/sequence/normal.json",
         "@type":"sc:Sequence",
         "label":"Normal Order",
         "canvases":[  
END;

$canvases = array();

foreach ($arr["info"] as $k => $a)
  {
  if (isset($a["height"]))
    {$h = $a["height"];}
  else
    {$h = 256;}
    
  if (isset($a["width"]))
    {$w = $a["width"];}
  else
    {$w = 256;}
    
  $id = $a["@id"];
  ob_start();
  echo <<<END

		{  
               "@id":"https://cima.ng-london.org.uk/zenodo/manifests/sequence/$arr[id]/normal.json",
               "@type":"sc:Canvas",
               "label":$arr[title],
               "height":$h,
               "width":$w,
               "images":[  
                  {  
                     "@type":"oa:Annotation",
                     "motivation":"sc:painting",
                     "resource":{  
                        "@id":"$id/full/full/0/default.jpg",
                        "@type":"dctypes:Image",
                        "format":"image/jpeg",
                        "height":$h,
                        "width":$w,
                        "service":{  
                           "@context":"http://iiif.io/api/image/2/context.json",
                           "@id":"$id",
                           "profile":"http://iiif.io/api/image/2/level2.json"
                        }
                     },
                     "on":"https://cima.ng-london.org.uk/zenodo/manifests/sequence/$arr[id]/normal.json"
                  }
               ]
            }
END;

  $canvases[] = ob_get_contents();
  ob_end_clean(); // Don't send output to client	
  }    
  
echo implode (", ", $canvases);

  echo <<<END
  }
         ]
      }
   ]
}

END;

	$json = ob_get_contents();
	ob_end_clean(); // Don't send output to client	

	return ($json);
	}
	

function addImageDets ($arr)
	{
	if (preg_match("/^(.+)([\/]full[\/].+default[.]jpg)$/", $arr["thumb"], $m))
		{$info = $m[1]."/info.json";
		 $arr["info"] = getRemoteJsonDetails ($info, false, true);}
		 
	return ($arr);
	}
	
function getRemoteJsonDetails ($uri, $format=false, $decode=false)
	{if ($format) {$uri = $uri.".".$format;}
	 $fc = file_get_contents($uri);
	 if ($decode)
		{$output = json_decode($fc, true);}
	 else
		{$output = $fc;}
	 return ($output);}

function getZenodoJsonDetails ($id, $decode=false)
	{
	$fc = file_get_contents($id."/export/json");
	$fc = explode("\n", $fc);
	$json = "";
	$echo = false;
	 
	foreach ($fc as $k => $line)
		{
		if ($line == "<pre style=\"white-space: pre-wrap;\">{")
			{$line = "{";
			 $echo = true;}
		else if ($line == "}</pre>")
			{$echo = false;
			 $json .= "}";}
		
		if ($echo)
			{$json .= $line."\n";}
		}
		
	$json = htmlspecialchars_decode($json);
	 
	if ($decode)
		{$output = json_decode($json, true);}
	else
		{$output = $json;}
		
	return ($output);
	}
	 
?>

