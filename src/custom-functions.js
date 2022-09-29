/**
 * Custom Functions
 */
export function doc_ids( arr ) {
    var doc_ids = '';
    if ( arr ) {
        for ( let i = 0; i < arr.length; i++ ) {
            let doc_split = arr[i].split('|')
            let doc_id = doc_split[0].trim()
            let comma = i === arr.length-1 ? '' : ','
            doc_ids += doc_id + comma
        }
    }
    return doc_ids
}
