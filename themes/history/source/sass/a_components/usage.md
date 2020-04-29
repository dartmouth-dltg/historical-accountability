#AGILE DESIGN SYSTEM: Components Folder

A component is considered a discreet, reusable set of SASS declarations. Items in a 00_core folder are intended to be loaded (in the sort order) before any other components. 

Components have the following structure:

- \_00\_configuration_\[component_name\]: Required variables for this component
- \_index.sass: Components must load their own partials and manage load order here.
_ \_index_noconfig: Use this as an index file if the configuration values are to be skipped.
- usage.md: Information about component usage.
- \_component.sass: A partial with any SASS to be included in the stylesheet.
- component.sass: Use if the the component outputs its own stylesheet. Must load required core libraries and components itself.
- package.json: Used to load vendor files via NPM. The \_index file must load and node\_modules that have been added.
- component.html: A usable DOM structure to be bundled with component.
- sample.html: A sample DOM structure meant for reference.
- requirements.yml.  A YML file outlining required assets (to be located elsewhere in the assets folder)
- assets. Onboard versions of those assets. For now they should be moved elsewhere.
- 10\_vendor : 3rd party files manually bundled with the component
- 11\_functions: Related SASS functions.
- 12\_mixins: Related SASS mixins
- 13\_definitions: Related SASS definitions. WARNING: Avoid definitions with variables from other components in 00\_core components, as they may be assigned the default value over the local one (local configs are loaded after). Instead, make a separate component and load it as part of a profile, ensuring local theme variables get used.  
- 13\_animations: Predefined animations to be included in the stylesheet. *NOTE: This is a special case as the other numbered folders do not directly output to the stylesheet. It may need to be refactored.

##Note on Configuration and Component Naming
In some instances components will require settings variables that are not available in core settings. In this case themes and profiles must manage their own configuration files. Components should be complete by default, so the standard reference must automatically include the required configuration file. The components must also give an option to load without the configuration file so the settings can be established locally.

Component  that require configuration should be indicated by a preceding exclamation point on their directory, i.e. ./!componentname. These components must provide TWO index files: \_index which loads the default configuration, and \_index_local_configuration which omits the config file. If themes and profiles do not want to use the standard configuration they must load a local copy of its configuration file before loading the \_index_local_configuration file.

An an architectural note – 00_Core components establish common variables and libraries should NOT have any primary CSS declarations. This way core settings can optionally be loaded locally without the (admittedly) inelegant solution of referencing a separate directory index.

##Note of Components with Layout and Typography
In many instances a component will make declarations across many categories – a component is typically some combination of layout, type and colour and it would be onerous to separate them out. In general, compound components come later in the categorical structure. A rule of thumb is that components may contain declarations belonging to previous categories, e.g. typography (30\_typography) may contain colour declarations (20\_colour), but not vice-versa.

For the most part cross-categorical components should be considered "site elements" or "features" and organized accordingly. When dealing with local themes these elements will also be tied to DOM blocks (see description of site elements below).

##Categories
Categories form the top-level folder structure.

###00\_general
Loads basic utilities and general vendor libraries used by other components.

###10\_layout
Establishes grid system and key structural components. Core loads the modular scale system.

###20\_colour
Handle sitewide colour definitions and colourization-related functions. Core loads the colour system and its default configuration values.

###30\_typography
Handles type styling. Emphasis on root and base HTML typography.

###40\_ui
Handles forms, buttons and other interactive components.

###50\_animations
Handles transitions and other animation.

###60\_site_elements
Components that affect behaviour sitewide. Elements are intended to be rolled into the global stylesheet, so all SASS files in this folder must be partials.
The local version of this folder will be the primary working folder for most themes. It should be contain a subfolder structure germane to the theme and/or overall framework.
Site elements should tied to DOM objects where possible, and named accordingly. It is important be mindful of discoverability when naming components and files – for the most part people will be working backwards from the Browser Inspector to find the applicable SASS file, and naming is a key facilitator in the process.

###70\_contexts
Styles intended to be loaded in a particular section or context of the site (e.g. an CMS admin area stylesheet). Can also be used to define styles for a distinct page of the site.
All SASS files should be standalone and manage their own partials. They also must import the _00_load_core.sass file and any dependent components/profiles independently.

###80\_features
Encapsulated components that generate their own css file. Features are intended to be injected into pages that use the associated component (e.g. a react component). Features should strive towards reuse on other apps and sites.
All SASS files should be standalone and manage their own partials. They also must import the _00_load_core.sass file and any dependent components/profiles independently.
Features can contain vendor files, including customizations or replacements of third-party library stylesheets.


##File Metadata
This is a work in progress. 

The commented headers of each file appear in the un-minified stylesheets. This allows for easier debugging of both load order and style declaration location. Here are some of the headers being considered. As of now there is redundancy here, as it would be onerous to require a full list of headers on so many SASS files. Use many as required for transparency and discoverability.

- @file The name of the file, e.g _index.sass
- @description The purpose of the file.
- @directory The full directory of the file, e.g. a\_components/10\_layout/00_core/
- @theme The name of the theme when applicable. You can also use "Local" as a value in instances where there is only one active theme.
- @component The name of the component, e.g. "basic_layout". You may also want to include the category here "10_layout/basic_layout"
- @group The major group, "a_components", "b_profiles", "c_local"
- @category The subdirectory in the group, e.g. "10_layout"