/**
 * Custom Functions
 */
export function doc_ids(arr) {
	if (!arr) {
		return '';
	}
	return arr
		.map((item) => {
			const index = item.indexOf('|');
			return (index === -1 ? item : item.substring(0, index)).trim();
		})
		.join(',');
}
