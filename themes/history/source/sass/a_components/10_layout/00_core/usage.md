#AGILE DESIGN SYSTEM CORE LAYOUT USAGE
A work in progress.

The goal of the ADS is to provide a flexible framework for implementing challenging designs. It leverages a modular scale to ensure that all layout and type elements remain in proportion. All spacing (e.g. margins and padding) and scaling (e.g. the difference in sizing between an h1 and h2 unit) are expressed as a relative value using the rv() function. Integers are passed to this function and are scaled proportionately.

The goal is to be able to set both base sizes and a proportion. It also allows you to control sizing at different breakpoints.

One way of thinking about the system is as a series of analog dials, with each key varibale modulating different aspects of the design while keeping everything in proportion.

## Key Variables

- $rootsize: Controls the size of the root HTML element. Usually expressed in an percentage. Higher percentages zooms everything in, lower percentages zoom sizes out.
- $blh: Sets the base spacing size relative to the $rootsize, expressed in rem units. Higher values create spaciousness, lower values create tightness.
- $scale: Sets the DIFFERENCE between sizes based on a modular scale. Integer values are calculated as a series of ratios based on $scale. Higher ratios create more pronounced differences between scaled elements (e.g. an h1 will be much larger than an h2), while lower ratios give more fine control of sizing but are more subtle in their distinctions.
- $basetypesize: Set in 20\_typography/00\_core/, you can also target the size of text elements relative to padding and margins. Spacing is relative to the <html> element (via $rootsize), while the +font-size() mixin sets type relative to the $basetypesize. 

## Breakpoints

The layout config also allows you to set named breakpoints for reference through the +bp() mixin. 
- $stack_breakpoint sets a named breakpoint where common layout elements typically stack vertically instead of horizontally. This is used by various components as a reference but is not strictly enforced in the layout.



