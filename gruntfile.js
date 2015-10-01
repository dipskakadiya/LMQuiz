'use strict';
module.exports = function ( grunt ) {

	// load all grunt tasks matching the `grunt-*` pattern
	// Ref. https://npmjs.org/package/load-grunt-tasks
	require( 'load-grunt-tasks' )( grunt );

	grunt.initConfig( {
		// SCSS and Compass
		// Ref. https://npmjs.org/package/grunt-contrib-compass
		compass: {
			frontend: {
				options: {
					config: 'config.rb',
					force: true
				}
			},
			// Admin Panel CSS
			backend: {
				options: {
					sassDir: 'css/sass/',
					cssDir: 'css/'
				}
			}
		},
		// Uglify
		// Compress and Minify JS files
		// Ref. https://npmjs.org/package/grunt-contrib-uglify
		/*uglify: {
			options: {
				banner: '/!*! \n * rtBiz Helpdesk JavaScript Library \n * @package rtBiz Helpdesk \n *!/'
			},
			frontend: {
				src: [
				],
				dest: ''
			},
			backend: {
				src: [
				],
				dest: ''
			},
		},*/
		// Watch for hanges and trigger compass and uglify
		// Ref. https://npmjs.org/package/grunt-contrib-watch
		watch: {
			compass: { files: [ '**/*.{scss,sass}' ],
				tasks: [ 'compass' ]
			},
			/*uglify: {
				files: [ '<%= uglify.frontend.src %>', '<%= uglify.backend.src %>', '<%= uglify.support.src %>','<%= uglify.shortcode.src %>'  ],
				tasks: [ 'uglify' ]
			}*/
		}
	} );

	// Register Task
	grunt.registerTask( 'default', [ 'watch' ] );
};
