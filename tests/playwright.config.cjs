module.exports = {
	testMatch: 'wordpress-smoke.cjs',
	reporter: 'line',
	outputDir: process.env.EZD_PLAYWRIGHT_OUTPUT || 'test-results',
	workers: 1,
	timeout: 40_000,
};
