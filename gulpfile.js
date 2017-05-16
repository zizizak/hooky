const babelify   = require('babelify')
const browserify = require('browserify')
const buffer     = require('vinyl-buffer')
const chalk      = require('chalk')
const fs         = require('fs')
const gulp       = require('gulp')
const gutil      = require('gulp-util')
const merge      = require('utils-merge')
const rename     = require('gulp-rename')
const source     = require('vinyl-source-stream')
const sourcemaps = require('gulp-sourcemaps')
const uglify     = require('gulp-uglify')
const vueify     = require('vueify')
const watchify   = require('watchify')

const map_error = err => {
  if(err.fileName){
    gutil.log(`
      ${chalk.red(err.name)}:
      ${chalk.yellow(err.fileName.replace(__dirname + '/assets/src'))}:
      ${chalk.magenta(err.lineNumber)} & Column
      ${chalk.magenta(err.columnNumber || err.column)}:
      ${chalk.blue(err.description)}
    `)
  } else {
    gutil.log(`${chalk.red(err.name)}: ${chalk.yellow(err.message)}`)
  }
}

gulp.task('watchify', () => {
  let args    = merge(watchify.args, {debug: true})
  let bundler = watchify(browserify('./assets/src/main.js', args))
    .transform(babelify, {presets: ['es2015']})
    .transform(vueify)

  bundle_js(bundler)
  bundler.on('update', () => bundle_js(bundler))
})

const bundle_js = bundler => {
  return bundler.bundle()
    .on('error', map_error)
    .pipe(source('main.js'))
    .pipe(buffer())
    .pipe(rename('hookah.min.js'))
    .pipe(sourcemaps.init({loadMaps: true}))
    .pipe(uglify())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('assets/dist'))
}
