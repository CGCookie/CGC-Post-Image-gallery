<?php

include_once('metabox/meta-box-3.2.class.php');
$pig_prefix = 'pig_';

$pig_meta_boxes = array();

$pig_meta_boxes[] = array(
    'id' => 'pig-image-meta',                    
    'title' => 'Image Meta',          
    'pages' => array('images'),   
    'context' => 'normal',       
    'priority' => 'high',              
    'fields' => array(                  
        array(
            'name' => 'Feature',    
            'desc' => 'Feature this Image?',    
            'id' => $pig_prefix . 'featured',            
            'type' => 'checkbox',  
        ),
		array(
            'name' => 'Won',    
            'desc' => 'Did this image win a contest?',    
            'id' => $pig_prefix . 'won',            
            'type' => 'checkbox',  
        ),
      array(
            'name' => 'Mature content',    
            'desc' => 'This image contains nudity or other mature content',    
            'id' => $pig_prefix . 'mature',            
            'type' => 'checkbox',  
      ),
		array(
            'name' => 'Place in Contest',    
            'desc' => '<br/>Which place did this image get?',    
            'id' => $pig_prefix . 'contest_place',            
            'type' => 'select', 
			'options' => array('First', 'Second', 'Third', 'Community Vote')
        ),
		array(
            'name' => 'Okay to Use',    
            'desc' => 'Can this image be used by CGC?',    
            'id' => $pig_prefix . 'okay_to_use',            
            'type' => 'text',  
        ),
		array(
            'name' => 'Posted On:',    
            'desc' => 'Post this image is attached to',    
            'id' => $pig_prefix . 'parent_post_name',            
            'type' => 'text',  
        ),
		array(
            'name' => 'Parent Post ID',    
            'desc' => 'The ID # of the parent post',    
            'id' => $pig_prefix . 'parent_post_id',            
            'type' => 'text',  
        ),
		array(
            'name' => 'Image URL',    
            'desc' => 'The URL of this Image',    
            'id' => $pig_prefix . 'image_url',            
            'type' => 'text',  
        ),
		array(
            'name' => 'Subsite ID',    
            'desc' => 'The subsite ID this image was posted on',    
            'id' => $pig_prefix . 'subsite_id',            
            'type' => 'text',  
        ),
		array(
            'name' => 'Subsite Image ID',    
            'desc' => 'The ID of the original image',    
            'id' => $pig_prefix . 'subsite_image_id',            
            'type' => 'text',  
        )
    )
);

foreach ($pig_meta_boxes as $meta_box) {
    new pig_meta_box($meta_box);
}