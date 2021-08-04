<?php

namespace AndaluWooCourses\handlebars;

/**
 * Renders a handlebars template.
 *
 * When looking for a handlebars template, this function will
 * check your theme to see if it has an
 * `andalu-woocources-templates/` directory. If not found, it
 * will check this plugin's `lib/templates/` directory.
 *
 * You may specify your templates using the name of the
 * template without the extension.
 *
 * IMPORTANT: This function compiles handlebars templates to
 * PHP. Therefore, you should never edit the files in
 * `lib/templates/compiled/`. To render a template, the
 * code compares the timestamps of the handlesbars template
 * and the compiled PHP template. If the handlebars template
 * is newer, it re-renders the compiled PHP template.
 *
 * @param      string  $filename  The filename of the handlebars template inside `/lib/templates/`.
 * @param      array   $data      An array of data to be rendered inside the handlebars template.
 *
 * @return     mixed   Returns the HTML string for your specifed template. Returns FALSE if your template is not found or emtpy.
 */
function render_template( $filename = '', $data = [] ){
  if( empty( $filename ) )
    return false;

  // Remove file extension
  $extensions = ['.hbs', '.htm', '.html'];
  $filename = str_replace( $extensions, '', $filename );

  $compile = 'false';

  $theme_path = \get_stylesheet_directory();
  $theme_template = \trailingslashit( $theme_path ) . 'andalu-woocourses-templates/' . $filename . '.hbs';
  $theme_template_compiled = \trailingslashit( $theme_path ) . 'andalu-woocourses-templates/compiled/' . $filename . '.php';

  $plugin_template = trailingslashit( ANDALU_DIR ) . 'lib/templates/' . $filename . '.hbs';
  $plugin_template_compiled = \trailingslashit( ANDALU_DIR ) . 'lib/templates/compiled/' . $filename . '.php';

  if( file_exists( $theme_template ) ){
    if( ! file_exists( $theme_template_compiled ) ){
      $compile = 'true';
    } else if( filemtime( $theme_template ) > filemtime( $theme_template_compiled ) ){
      $compile = 'true';
    }

    $template = $theme_template;
    $template_compiled = $theme_template_compiled;
  } else if( file_exists( $plugin_template ) ){
    if( ! file_exists( $plugin_template_compiled ) ){
      $compile = 'true';
    } else if( filemtime( $plugin_template ) > filemtime( $plugin_template_compiled ) ){
      $compile = 'true';
    }

    $template = $plugin_template;
    $template_compiled = $plugin_template_compiled;
  } else if( ! file_exists( $plugin_template ) ){
    return false;
  }

  $template = [
    'filename' => $template,
    'filename_compiled' => $template_compiled,
    'compile' => $compile,
  ];

  if( ! file_exists( dirname( $template['filename_compiled'] ) ) )
    \wp_mkdir_p( dirname( $template['filename_compiled'] ) );

  if( 'true' == $template['compile'] ){
    $hbs_template = file_get_contents( $template['filename'] );
    $phpStr = \LightnCandy\LightnCandy::compile( $hbs_template, [
      'flags' => \LightnCandy\LightnCandy::FLAG_SPVARS | \LightnCandy\LightnCandy::FLAG_PARENT | \LightnCandy\LightnCandy::FLAG_ELSE
    ] );
    if ( ! is_writable( dirname( $template['filename_compiled'] ) ) )
      \wp_die( 'I can not write to the directory.' );
    file_put_contents( $template['filename_compiled'], '<?php' . "\n" . $phpStr . "\n" . '?>' );
  }

  if( ! file_exists( $template['filename_compiled'] ) )
    return false;

  $renderer = include( $template['filename_compiled'] );

  return $renderer( $data );
}

/**
 * Checks if template file exists.
 *
 * @since 1.4.6
 *
 * @param string $filename Filename of the template to check for.
 * @return bool Returns TRUE if template file exists.
 */
function template_exists( $filename = '' ){
  if( empty( $filename ) )
  return false;

  // Remove file extension
  $extensions = ['.hbs', '.htm', '.html'];
  $filename = str_replace( $extensions, '', $filename );

  $theme_path = \get_stylesheet_directory();
  $theme_template = \trailingslashit( $theme_path ) . 'andalu-woocourses-templates/' . $filename . '.hbs';

  $plugin_template = trailingslashit( ANDALU_DIR ) . 'lib/templates/' . $filename . '.hbs';

  if( file_exists( $theme_template ) ){
    return true;
  } else if( file_exists( $plugin_template ) ){
    return true;
  } else if( ! file_exists( $plugin_template ) ){
    return false;
  }
}
