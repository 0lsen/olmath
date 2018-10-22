# oL Math

A tiny PHP toolkit to calculate stuff, with the ultimate goal of implementing a couple of numeric algorithms (CG, LSQR, LSMR, ...).

## What...
- [... is this about?](#what-is-this-about)
- [... does it do?](#what-does-it-do)
- [... is coming?](#what-is-coming)

#### What is this about?

Ever worked with [MATLAB](mathworks.com)? It's pretty good for your everyday numerical matrix-vector calculation needs.
How does it do that? Curiosity about how to calculate stuff quickly and efficiently drives me to just try myself and build my own little clone.
_(
How do you build/use a sparse matrix? 
How does one calculate stuff with rational/complex numbers?
)_

The focus for now is on functionality that is necessary for "numerical needs".
E.g. Matrix-Matrix-multiplication is _definitely not_ a priority since you just should _not_ need to do that in a numerical context.

_Why PHP?_ I feel comfortable with it, that's it. 
I doubt it's even a very smart choice. There most likely are many languages better suited for parallelisation and reliably handling numbers.
But I'm in this for the learning experience and getting back in touch with Numerics.

#### What does it do?

###### Stuff with numbers:

- Works with real, rational and complex numbers. 
_(
Let's work with 1/3 instead of 0.333... 
Not sure if that was ever a real problem or creates more, tbh.
)_
- Do the calculations deemed necessary.
_(
E.g. there's no modulo function - I have yet to see a use for that.
)_

###### Stuff with vectors/matrices:

- Provides full and sparse implementations.
_(
Essential to numeric stuff. So far sparse operations even seem quite elegant.
)_
- Do the calculations deemed necessary.
_(
As mentioned doing certain things should be avoided. So they aren't implemented in the first place.
)_
 
###### Parse stuff:

- Evaluates math expressions based on numbers (so far).

#### What is coming?

##### soon™

- Matrix/vector operations that seem necessary at the moment.
- Parser for matrix/vector expressions.
- Some numeric algorithms, finally?

##### later

- **Web frontend** (separate project) - Would only be a representation of "send input string to Parser-API and display result".
Maybe I'll challenge myself to use a JS Framework that isn't my familiar Typescript/jQuery, let's see...
- **Serious Optimisation** - There's probably _loads_ to optimise. 
Handling sparse things (zero elimination), special matrix structures (diagonal / square / symmetric / ...), estimations for stuff I haven't even implemented yet, ...
- **Metrics** - Why did I do this in the first place? 
"to calculate stuff quickly and efficiently" - Did I accomplish that?
_(How do sparse implementations compare to full ones? Will a Least Squares Problem be solved reasonably quickly?)_
- **Parallelisation** - Matrix operations greatly benefit from multiple threads.
_(E.g. a matrix-vector-product's elements can be computed independently.)_

##### TODOs as needed

- necessary algorithms as they pop up
- SparseMatrix creation - possibly inspired by how they can be built in MATLAB.
- map matrix/vector external indices `1:n` to internal ones `0:n-1`, e.g. for effective/transparent sparse construction
_( I want to avoid an array "key shift": do vector/matrix constructions even occur often? is a laborious "key shift" even costly? )_
- maintain reasonable accuracy
_( maintain/create rational numbers whenever reasonable, use relative accuracy instead of decimal accuracy, ... )_