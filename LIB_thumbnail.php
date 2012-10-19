<?php


/*
#-----------------------------------------------------------------------
#
# LIB_thumbnail     JPG Thumbnailing routine
#
#-----------------------------------------------------------------------

create_thumbnail($org_file, $new_file_name, $max_width, $max_height)
-------------------------------------------------------------
DESCRIPTION:
		Creates a thumbnail image of a larger image

INPUT:
        $org_file
            The name of the original image file

        $new_file_name
            The name of the thumbnail image file

        $max_width
            The maximum width of the thumbnail file
        $max_height
            The maximum height of the thumbnail file
RETURNS:
		Creates a thumbnail file with the file name $new_file_name
#########################################################################
*/
function create_thumbnail($org_file, $new_file_name, $max_width, $max_height)
    {
	// Initialization
	$src_image_array = getimagesize ($org_file);
	$srcX = 1;
	$srcY = 1;
	$srcW = $src_image_array[0];
	$srcH = $src_image_array[1];

    # If images is wider than it is tall
    if($srcW>$srcH)
        {
        $dstX = 1;
		$dstY = 1;
        if($srcW>$max_width)
    	    $dstW = $max_width;
        else
    	    $dstW = $srcW;
    	$ratio = $srcW/$srcH;
		$dstH  = $dstW/$ratio;
        }
    # Else if the images is taller than it is wide
    else
        {
        $dstX = 1;
		$dstY = 1;
        if($srcH>$max_width)
    	    $dstH = $max_width;
	    else
    	    $dstH = $srcH;
    	$ratio = $srcH/$srcW;
        $dstW  = $dstH/$ratio;
        }
    $src_image = ImageCreateFromJPEG ($org_file);
    $dst_image = imagecreatetruecolor($dstW, $dstH) or die ("Cannot Initialize new GD image stream");
    $result = imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH );
	$bool = imagejpeg ($dst_image, $new_file_name);		// create thumbnail image

    imagedestroy($src_image);
    imagedestroy($dst_image);
    return $bool;
    }
?>
