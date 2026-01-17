<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Component Locations
    |---------------------------------------------------------------------------
    |
    | This value sets the root directories that'll be used to resolve view-based
    | components like single and multi-file components. The make command will
    | use the first directory in this array to add new component files to.
    |
    */

    'component_locations' => [
        resource_path('views/components'),
        resource_path('views/livewire'),
    ],

    /*
    |---------------------------------------------------------------------------
    | Component Namespaces
    |---------------------------------------------------------------------------
    |
    | This value sets default namespaces that will be used to resolve view-based
    | components like single-file and multi-file components. These folders'll
    | also be referenced when creating new components via the make command.
    |
    */

    'component_namespaces' => [
        'layouts' => resource_path('views/layouts'),
        'pages' => resource_path('views/pages'),
    ],

    /*
    |---------------------------------------------------------------------------
    | Page Layout
    |---------------------------------------------------------------------------
    | The view that will be used as the layout when rendering a single component as
    | an entire page via `Route::livewire('/post/create', 'pages::create-post')`.
    | In this case, the content of pages::create-post will render into $slot.
    |
    */

    'component_layout' => 'layouts.app',

    /*
    |---------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |---------------------------------------------------------------------------
    | Livewire allows you to lazy load components that would otherwise slow down
    | the initial page load. Every component can have a custom placeholder or
    | you can define the default placeholder view for all components below.
    |
    */

    'component_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Make Command
    |---------------------------------------------------------------------------
    | This value determines the default configuration for the artisan make command
    | You can configure the component type (sfc, mfc, class) and whether to use
    | the high-voltage emoji as a prefix in the sfc|mfc component names.
    |
    */

    'make_command' => [
        'type' => 'class',
        'emoji' => false,
    ],

    /*
    |---------------------------------------------------------------------------
    | Class Namespace
    |---------------------------------------------------------------------------
    |
    | This value sets the root class namespace for Livewire component classes in
    | your application. This value will change where component auto-discovery
    | finds components. It's also referenced by the file creation commands.
    |
    */

    'class_namespace' => 'App\\Http\\Livewire',

    /*
    |---------------------------------------------------------------------------
    | Class Path
    |---------------------------------------------------------------------------
    |
    | This value is used to specify the path where Livewire component class files
    | are created when running creation commands like `artisan make:livewire`.
    | This path is customizable to match your projects directory structure.
    |
    */

    'class_path' => app_path('Http/Livewire'),

    /*
    |---------------------------------------------------------------------------
    | View Path
    |---------------------------------------------------------------------------
    |
    | This value is used when creating new components to set the view path. It
    | will be used to create both the path and the namespace of the view.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    |
    | Livewire handles file uploads by storing uploads in a temporary directory
    | before the file is validated and stored permanently. All file uploads
    | are directed to a global endpoint for temporary storage. The config
    | items below are used for customizing the way the endpoint works.
    |
    */

    'temporary_file_upload' => [
        'disk' => null,
        'rules' => null,
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5,
    ],

    /*
    |---------------------------------------------------------------------------
    | Pagination Theme
    |---------------------------------------------------------------------------
    |
    | When enabling Livewire's pagination feature by using the `WithPagination`
    | trait, Livewire will use Tailwind templates for the page links. If you
    | want Bootstrap CSS, you can specify it here or per-component.
    |
    */

    'pagination_theme' => 'tailwind',

    /*
    |---------------------------------------------------------------------------
    | Inject Assets
    |---------------------------------------------------------------------------
    |
    | By default, Livewire injects its JavaScript and CSS assets into the page.
    | If you're using a CDN or want to manually include the assets, you can
    | disable this behavior by setting this option to false.
    |
    */

    'inject_assets' => true,

];
