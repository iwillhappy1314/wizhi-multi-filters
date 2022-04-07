# Wenprise Content Types

Create WordPress content types and taxonomies more easier.

## Register Post type

```php
wprs_types( "work", "Works", [ 'title', 'editor', 'thumbnail'], true, false, 'dashicons-art' );
```
### Params

```@param string  $slug         Post type slug
 @param string  $name         Post type name in the menu and page title
 @param array   $support      The functions post type support
 @param boolean $is_publish   Is publish in frontend
 @param boolean $hierarchical is hierarchical
 @param string  $icon         the dashicon of the dashboard menu
```
### Filters
 
 - wprs_type_labels_$slug: modify the post type labels
 - wprs_type_args_$slug: modify the args to register the post type

## Register Taxonomy

```php
wprs_tax( "work_type", 'work', "Work Type", true );
```
### Params

```
@param string       $tax_slug     Taxonomy slug
@param string|array $post_type    the post type of the taxonomy registered to 
@param string       $tax_name     Taxonomy name 
@param boolean      $hierarchical is hierarchical
```

### Filters
  
 - wprs_type_labels_$slug: modify the post type labels
 - wprs_type_args_$slug: modify the args to register the post type
 - wprs_tax_types_$slug: Modify the post types taxonomy registered to

