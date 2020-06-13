<?php
header('Content-Type: text/plain');
$json=file_get_contents('https://fernandafamiliar.soy/wp-json/wp/v2/posts');
$array=json_decode($json);
//echo print_r($array,true);

require_once('wp-blog-header.php');
require_once( 'wp-admin/includes/image.php' );

foreach($array as $articulo)
{

    $titulo=$articulo->title->rendered;
    $contenido=$articulo->content->rendered;
    $post_excerpt = $articulo->excerpt->rendered;
    $post_date = $articulo->date;
    $post_modified_date = $articulo->modified;
    $post=array(
        'post_title'=>$titulo,
        'post_content'=>$contenido,
        'post_status'   => 'publish',
        'post_excerpt'   => $post_excerpt,
        'post_date' => $post_date
    );
    $id = wp_insert_post($post);

    //print_r($articulo);
    //exit;

    echo print_r('Inserted ID: '.$id."\n",true);

    // Add custom link
    add_post_meta( $id, 'external_link', $articulo->link, true);

    // Get / save images
    $featuredMediaLink = $articulo->_links->{"wp:featuredmedia"}[0]->href;
    $featuredMedia = json_decode(file_get_contents($featuredMediaLink));
    //echo print_r($featuredMedia->media_details->sizes->full,true);
    $uploaddir = wp_upload_dir();
    $filename = $featuredMedia->media_details->sizes->full->file;
    $uploadfile = $uploaddir['path'] . '/' . $filename;
    $contents= file_get_contents($featuredMedia->media_details->sizes->full->source_url);
    $savefile = fopen($uploadfile, 'w');
    fwrite($savefile, $contents);
    fclose($savefile);

    // Insert into media library
    $wp_filetype = wp_check_filetype(basename($filename), null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => $filename,
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attach_id = wp_insert_attachment( $attachment, $uploadfile, $id);
    $imagenew = get_post( $attach_id );
    $fullsizepath = get_attached_file( $imagenew->ID );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
    wp_update_attachment_metadata( $attach_id, $attach_data );
    set_post_thumbnail( $id, $attach_id );


}