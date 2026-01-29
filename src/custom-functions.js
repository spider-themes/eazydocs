/**
 * Custom Functions
 */
export function doc_ids(arr) {
	if (!arr) {
		return '';
	}
	const items = Array.isArray(arr) ? arr : (typeof arr === 'string' ? arr.split(',') : [arr]);
	return items
		.map((item) => {
			const str = String(item);
			const index = str.indexOf('|');
			return (index === -1 ? str : str.substring(0, index)).trim();
		})
		.join(',');
}
