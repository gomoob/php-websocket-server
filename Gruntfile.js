var fs = require('fs');

module.exports = function(grunt) {

    grunt.initConfig(
        {
            
            /**
             * Reads the 'package.json' file and puts it content into a 'pkg' Javascript object.
             */
            pkg : grunt.file.readJSON('package.json'),
            
            /**
             * Clean task.
             */
            clean : ['target'],
            
            /**
             * Shell Task
             */
            shell : {
                
            	'check-php-memory_limit' : {
            		command : 'php -r \'exit(intval(ini_get("memory_limit")) < 512 ? -1 : 0);\'',
            		options : {
            			callback : function(err, stdout, stderr, cb) {
            				if(err) {
            					grunt.log.error('');
            					grunt.log.error(
            					    'Your PHP memory_limit setting is too low, you should set it to at least 512M !'
            						['red']
            					);
            					grunt.log.error('');
            					cb(new Error());
            					return ;
            				}
            				cb();
            			},
            			stderr : false,
                		stdin : false,
                		stdout : false
            		}
            	},
                
                pdepend : {
                    command : (function() {
                        
                        var command = 'php vendor/pdepend/pdepend/src/bin/pdepend';
                        command += ' --jdepend-chart=target/reports/pdepend/jdepend-chart.svg';
                        command += ' --jdepend-xml=target/reports/pdepend/jdepend.xml';
                        command += ' --overview-pyramid=target/reports/pdepend/overview-pyramid.svg';
                        command += ' --summary-xml=target/reports/pdepend/summary.xml';
                        command += ' src/main/php';
                        
                        return command;

                    })()
                },
                
                phpcpd : {
                    command : 'php vendor/sebastian/phpcpd/phpcpd src/main/php'
                },

                phpcbf : {
                    command : function() {
                        
                        var command = 'php ./vendor/squizlabs/php_codesniffer/scripts/phpcbf';
                        command += ' --standard=PSR2';
                        command += ' --no-patch';
                        command += ' src/main/php';
                        command += ' src/test/php';

                        return command;
                        
                    }
                },
                
                phpcs : {
                    command : function() {
                        
                        var command = 'php ./vendor/squizlabs/php_codesniffer/scripts/phpcs';
                        command += ' --standard=PSR2';
                        command += ' -v';
                        
                        if(grunt.option('checkstyle') === true) {
                            
                            command += ' --report=checkstyle';
                            command += ' --report-file=target/reports/phpcs/phpcs.xml'; 
                        }

                        command += ' src/main/php';
                        command += ' src/test/php/Gomoob';

                        return command;
                        
                    }
                },
                
                phpdocumentor : {
                    command : 'php vendor/phpdocumentor/phpdocumentor/bin/phpdoc --target=target/reports/phpdocumentor --directory=src/main/php'
                },
                
                phploc : {
                    command : 'php vendor/phploc/phploc/phploc src/main/php'
                },
                
                phpmd : {
                    command : (function() {
                        
                        var command = 'php vendor/phpmd/phpmd/src/bin/phpmd ';
                        command += 'src/main/php ';
                        command += 'html ';
                        command += 'cleancode,codesize,controversial,design,naming,unusedcode ';
                        command += '--reportfile=target/reports/phpmd/phpmd.html';

                        return command;

                    })(),
                    options : {
                        callback : function(err, stdout, stderr, cb) {
                            grunt.file.write('target/reports/phpmd/phpmd.html', stdout);
                            cb();
                            
                        }
                    }
                },
                
                phpmetrics : {
                	command : 'php vendor/phpmetrics/phpmetrics/bin/phpmetrics --config phpmetrics.yml'
                },
                
                phpunit : {
                    command : (function() {
                        var command = 'php vendor/phpunit/phpunit/phpunit ';
                        
                        // If the 'ci' option is configured we read the 'phpunit.xml' file. The 'ci' option is dedicated 
                        // to unit testing on the continuous integration server, it enables several reports generation.
                        if(grunt.option('ci')) {
                            
                            command += '-c phpunit.xml ';

                        }

                        command += '--bootstrap src/test/php/bootstrap.php ';
                        command += '--colors=always ';
                        command += '--no-globals-backup ';
                        command += '--stop-on-error ';
                        command += '--stop-on-failure ';
                        command += '--verbose ';
                        command += '--debug ';
                        //command += '--group WebSocketClientMockTest.testFindByTags ';
                        command += 'src/test/php';

                        return command;

                    })()

                }
            
            } /* shell Task */

        }

    ); /* Grunt initConfig call */

    // Load the Grunt Plugins    
    require('load-grunt-tasks')(grunt);

    // When PDepend is executed it requies an existing output directory
    grunt.registerTask('before-pdepend' , 'Creating directories required by PDepend...', function() {

        if(!fs.existsSync('target')) {
            fs.mkdirSync('target');
        }

        if(!fs.existsSync('target/reports')) {
            fs.mkdirSync('target/reports');
        }

        if(!fs.existsSync('target/reports/pdepend')) {   
            fs.mkdirSync('target/reports/pdepend');
        }

    });

    // When PHP_CodeSniffer is executed it requires an existing output directory
    grunt.registerTask('before-phpcs', 'Creating directories required by PHP Code Sniffer...', function() {
        
        if(grunt.option('checkstyle') === true) {

            if(!fs.existsSync('target')) {
                fs.mkdirSync('target');
            }
            
            if(!fs.existsSync('target/reports')) {
                fs.mkdirSync('target/reports');
            }

            if(!fs.existsSync('target/reports/phpcs')) {   
                fs.mkdirSync('target/reports/phpcs');
            }

        }
        
    });
    
    grunt.registerTask('before-phpmd', 'Creating directories required by PHP Mess Detector...', function() {
       
        if(!fs.existsSync('target')) {
            fs.mkdirSync('target');
        }

        if(!fs.existsSync('target/reports')) {
            fs.mkdirSync('target/reports');
        }

        if(!fs.existsSync('target/reports/phpmd')) {   
            fs.mkdirSync('target/reports/phpmd');
        }
        
    });
    
    /**
     * Task used to automatically fix PHP_CodeSniffer errors.
     */
    grunt.registerTask('phpcbf', ['shell:phpcbf']);
    
    /**
     * Task used to check the code using PHP_CodeSniffer.
     */
    grunt.registerTask('phpcs', ['before-phpcs', 'shell:phpcs']);
    
    grunt.registerTask('pdepend', ['before-pdepend', 'shell:pdepend']);
    
    grunt.registerTask('phpmd', ['before-phpmd', 'shell:phpmd']);

    /**
     * Task used to create the project documentation.
     */
    grunt.registerTask(
        'generate-documentation',
        [
            'phpcs', 
            'pdepend',
            'phpmd',
            'shell:phploc',
            'shell:phpcpd',
            'shell:phpmetrics',
            'shell:phpdocumentor' 
        ]
    );

    /**
     * Task used to execute the project tests.
     */
    grunt.registerTask('test', [/*'shell:check-php-memory_limit',*/ 'shell:phpunit']);

    /**
     * Default task, this task executes the following actions :
     *  - Clean the previous target folder 
     */
    grunt.registerTask('default', ['clean', 'phpcs', 'test']);

};
