<div class="moove-importer-plugin-documentation">
    <br>
    <h1><?php _e( 'Moove Feed Importer Plugin' , 'import-xml-feed' ); ?></h1>
    <p><?php _e('This plugin adds the ability to import content from an external XML/RSS file, or from an uploaded XML/RSS and add the content to any post type in your WordPress install. <br />It also supports importing taxonomies alongside posts.','import-xml-feed');?></p>
    <h3 id="the-process-of-import"><?php _e('The process of import:','import-xml-feed');?></h3>
    <ol style="list-style-type: decimal">
        <li><?php _e('Select the source ( URL or FILE UPLOAD )','import-xml-feed');?></li>
        <li><?php _e('Select your repeated XML element you want to import - This should be the node in your XML file which will be considered a post upon import.','import-xml-feed');?></li>
        <li><?php _e('Select the post type you want to import the content to.','import-xml-feed');?></li>
        <li><?php _e('Match the fields from the XML node you\'ve selected (step 2) to the corresponding fields you have available on the post type.','import-xml-feed');?></li>
    </ol>
    <h3 id="xml-files-and-urls"><?php _e('XML files and URLs','import-xml-feed');?></h3>
    <p><?php _e('The XML source file should be a valid XML file. The plugin does check if the URL source or the Uploaded file is valid for import and processing. If you use the URL source for importing, please make sure the URL you are using is not password protected with HTTP Auth or any other form of authentification (it needs to be public).','import-xml-feed');?></p>
    <p><?php _e('Accepted formats: XML 1.0, XML 2.0, Atom 1, RSS','import-xml-feed');?></p>
    <h3 id="xml-preview"><?php _e('XML Preview','import-xml-feed');?></h3>
    <p><?php _e('After sucessfully uploading an XML file or reading an external URL, the plugin will present you with an XML preview of the selected node, which can be used to check if you\'ve selected the correct node or you have all the data read correctly by the plugin. This preview presents one item from the selected node and it is paginated so you can navigate back and forward between the elements.','import-xml-feed');?></p>
    <h3 id="linking-taxonomies-to-posts"><?php _e('Linking Taxonomies to Posts','import-xml-feed');?></h3>
    <p><?php _e('This plugin allows you to import categories/taxonomies from the XML file and link the imported posts to these taxonomies.','import-xml-feed');?></p>
    <p><?php _e('First you need to have the taxonomies created in WordPress to allow the plugin to import into these taxonomies. By default WordPress has two taxonomies: categories and tags.','import-xml-feed');?></p>
    <p><strong><?php _e('Importing and linking multiple taxonomies to one post','import-xml-feed');?></strong></p>
    <p><?php _e('To import and link one post to multiple taxonomies, you need to have an XML element in your selected node with a list of categories separated by commas. These elements will be recognized and imported separately as taxonomy terms.','import-xml-feed');?></p>
</div>
<!-- moove-activity-plugin-documentation -->