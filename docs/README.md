![image](https://cloud.githubusercontent.com/assets/1245473/16253761/85daa7b2-3839-11e6-833e-6ec2bc15756b.png)

Semantic Breadcrumb Links (SBL) will try to generate a breadcrumb trail if a property relation can be matched
including its relational directionality. `>` indicates a `Has parent` (or `Is child of`) relationship while
`<` describes the closest descendant for a `Is parent of` affinity.

It is required to specify a property search pattern (by default `Has parent page` is assigned
as special property to `NS_MAIN`) in order to find relationships between a subject and its
antecedents.

### Example

`Foo` `>` `Bar` -- `[[Has parent page::Foo]]` `>` `Baz` -- `[[Has parent page::Bar]]`

If a subject `Baz` declares a relationship with `Bar` and `Bar` itself specifies
a parental relationship with `Foo` then the breadcrumb trail for `Baz` will be resolved as
`Foo > Bar`. On the other hand, the subject `Bar` will display a `Foo > Bar < Baz` trail
indicating that `Foo` is a `parent`( `>` ) and `Baz` is a `child` ( `<` ) of `Bar`.

Using `__NOBREADCRUMBLINKS__` on an individual page will suppress the __display__ of a SBL
generated breadcrumb trail, yet it will not remove or alter the state of annotation related
values.

### Configuration

Details on available settings are decribed in the [SBL configuration guide](00-configurations.md).
