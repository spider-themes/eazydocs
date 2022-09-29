<?php
if ( ! is_admin() ) {

    $col = !empty($attributes['col']) ? "col='{$attributes['col']}'" : '';

    $docs_with_comma = '';
    if ( !empty($attributes['include']) ) {
        $include_docs_count = count($attributes['include']);
        foreach ( $attributes['include'] as $i => $doc ) {
            $doci = $i + 1;
            $doc_split = explode('|', $doc);
            $doc_id = str_replace( ' ', '', $doc_split[0] );
            $comma = $doci == $include_docs_count ? '' : ',';
            $docs_with_comma .= $doc_id.$comma;
        }
    }

    $include = "include='$docs_with_comma'";

    echo do_shortcode('[eazydocs col="3" include="8364,2563,2484"]');
}