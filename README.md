# Magento 2 - Static CMS

Tool for creating static pages and blocks for Magento 2.

## Install

    composer require ubermanu/magento2-static-cms

## Format

The file contains 2 parts: header and content.

### Page

```
---
is_active: 1
identifier: home
---
<p>An updated home page.</p>
```

### Block

```
---
is_active: 1
title: Home page
content_heading: Home Page
identifier: home
page_layout: 1column
---
<p>CMS homepage content goes here.</p>
```

## Usage

Import a page with static content:

    php bin/magento cms:static:import --type page <file>

## FrontMatter

The FrontMatter part of the file is optional.<br>
But it's recommended to use it to set the model properties.

```
---
is_active: 1
title: Home page
content_heading: Home Page
identifier: home
page_layout: 1column
---
<p>CMS homepage content goes here.</p>
```

## Import directories

It's possible using xargs, remember to split your files by type:

    find <your-dir> -type f -print0 | xargs -l -0 php bin/magento cms:static:import --type page
