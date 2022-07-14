const purgecss = require('@fullhuman/postcss-purgecss')

module.exports = {
	plugins: [
		purgecss({
			content: [
				'../*.php',
				'../*/*.php',
				'../*/*/*.php',
				'../*/*/*/*.php',
				'./src/*.js',
				'./src/*/*.js',
				'./src/*/*/*.js',
				'./src/*/*/*/*.js',
				'./src/*.css',
				'./src/styles/*.css',
				'./src/styles/*/*.css',
				'./src/styles/*/*/*.css',
				'./src/js/*.css',
				'./src/js/*/*.css',
				'./src/js/*/*/*.css',
				'./node_modules/nouislider/dist/*.css',
				'./node_modules/bootstrap/js/src/*.js',
			],
		})
	]
}