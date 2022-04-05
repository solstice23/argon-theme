const purgecss = require('@fullhuman/postcss-purgecss')


module.exports = {
	plugins: [
		purgecss({
			content: [
				'../*.php',
				'../*/*.php',
				'./src/*.js',
				'./src/*/*.js',
				'./src/*/*/*.js',
				'./src/*/*/*/*.js',
				'./src/*.css',
				'./src/js/*.css',
				'./src/js/*/*.css',
				'./src/js/*/*/*.css',
				'./node_modules/nouislider/dist/*.css',
				'./node_modules/bootstrap/js/src/*.js',
				'./node_modules/tippy.js/dist/*.css',
			],
		})
	]
}