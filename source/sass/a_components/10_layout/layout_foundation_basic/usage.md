#AGILE LAYOUT FOUNDATION

The agile layout foundation is a tier-based system designed for maximum flexibility. It's extremely lightweight, and relies on the designer to handle their own grids (see Post-grid? below). It uses a rudimentary CSS grid. See the sample HTML.

##Tiers
All top level <body> elements (i.e.. body > *) are considered tiers. Tiers span the full width of their devices, with automatic proportional padding. Tiers can also be designated by adding a .tier class.

Tiers are automatically inset to a centred column with a $max_width. Inset tiers expect an single immediate child <div> as a container for content.

Tiers can be designated as full-width (i.e. NOT inset) by adding a .full class. Full-width tiers do not necessarily require a single child <div> container for content.

##Sidebars
Top-level sidebars must be designated as Asides (<aside>) and sit alongside a central <div>. There can be two asides (right and left). Sidebars do not float but are defined in a grid – floating asides should be handled individually. The stylesheet adapts its central column width depending on the number of asides (if any).

##Post-grid?
A standard grid layout defines a set number of columns and gaps and scales them responsively. The Agile layout foundation thinks a little differently. All spacing is proportional to the base $scale (which is also used to proportion type) using the rv(\[int\]) function.

Columns are left up to the designer – but should also be a fraction of the available space (3-columns at 1/3, 4 columns at 1/4, .etc) with rv() padding/margins for separation. Stacking elements on mobile can be handled manually. General device-width responsiveness should be driven by the top-level boxes, including .tiers (at 100%) and insets (max-width and legible text insets), with any columns being proportional to them.

In some instances an column (say, a high-level sidebar) needs a fixed width. Using $proportional_width_unit variable is the default approach; $proportional_width_unit provides a standard width as a percentage of available space that's proportional to the overall $scale. The higher the site’s $scale, the wider the $proportional_width_unit, as higher scale sites have increased spaciousness. Local overrides could also express this as a fraction.

##Stacking
Stacking occurs at the $stack_breakpoint. In most instances element stacking must be handled individually.