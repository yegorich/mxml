<?php
//
// "$Id: index.php,v 1.1 2004/05/17 02:14:55 mike Exp $"
//
// Mini-XML home page...
//

include_once "phplib/html.php";
include_once "phplib/db.php";

html_header();

?>

<h1 class="title" align="center">Mini-XML Home Page</h1>

<p class="title" align="center">Current Release: v1.3, December 21, 2003<br/>
[&nbsp;<a
href="mxml-1.3.tar.gz">Download&nbsp;Source&nbsp;(.tar.gz&nbsp;82k)</a>
| <a
href="mxml-1.3-1.i386.rpm">Download&nbsp;Linux&nbsp;RPM&nbsp;(.i386.rpm&nbsp;76k)</a>
| <a href="CHANGES">Change&nbsp;Log</a> | <a
href="documentation.html">Documentation</a> | <a
href="http://freshmeat.net/projects/mxml">Rate/Make&nbsp;Comments</A>&nbsp;]</p>

<h2>Introduction</h2>

<p>Mini-XML is a small XML parsing library that you can use to
read XML and XML-like data files in your application without
requiring large non-standard libraries. Mini-XML only requires
an ANSI C compatible compiler (GCC works, as do most vendors'
ANSI C compilers) and a "make" program.</p>

<p>Mini-XML provides the following functionality:</p>

<ul>
	<li>Reading and writing of UTF-8 encoded XML files and
	strings.</li>
	<li>Data is stored in a linked-list tree structure,
	preserving the XML data hierarchy.</li>
	<li>Supports arbitrary element names, attributes, and
	attribute values with no preset limits, just available
	memory.</li>
	<li>Supports integer, real, opaque ("cdata"), and text
	data types in "leaf" nodes.</li>
	<li>Functions for creating and managing trees of data.</li>
	<li>"Find" and "walk" functions for easily locating and
	navigating trees of data.</li>
</ul>

<p>Mini-XML doesn't do validation or other types of processing
on the data based upon schema files or other sources of
definition information, nor does it support character entities
other than those required by the XML specification. Also, since
Mini-XML does not support the UTF-16 encoding, it is technically
not a conforming XML consumer/client.</p>

<h2>Building Mini-XML</h2>

<p>Mini-XML comes with an autoconf-based configure script; just
type the following command to get things going:</p>

<pre>
./configure
</pre>

<p>The default install prefix is /usr/local, which can be
overridden using the --prefix option:</p>

<pre>
./configure --prefix=/foo
</pre>

<p>Other configure options can be found using the --help
option:</p>

<pre>
./configure --help
</pre>

<p>Once you have configured the software, type "make" to do the
build and run the test program to verify that things are
working, as follows:</p>

<pre>
make
</pre>

<h2>Installing Mini-XML</h2>

<p>The "install" target will install Mini-XML in the lib and
include directories:</p>

<pre>
make install
</pre>

<p>Once you have installed it, use the "-lmxml" option to link
your application against it.</p>

<h2>Documentation</h2>

<p>The documentation is currently a work in progress. Aside from
the information that follows, the <a
href="documentation.html">documentation</a> page provides a
handy reference and is automatically generated using Mini-XML.
You can also look at the <tt><a
href="testmxml.c">testmxml.c</a></tt> and <tt><a
href="mxmldoc.c">mxmldoc.c</a></tt> source files for examples of
using Mini-XML.</p>

<h3>The Basics</h3>

<p>Mini-XML provides a single header file which you include:</p>

<pre>
<a href="mxml.h">#include &lt;mxml.h></a>
</pre>

<p>Nodes are defined by the <a
href="documentation.html#mxml_node_t"><tt>mxml_node_t</tt></a>
structure; the <a
href="documentation.html#mxml_type_t"><tt>type</tt></a> member
defines the node type (element, integer, opaque, real, or text)
which determines which value you want to look at in the <a
href="documentation.html#mxml_value_t"><tt>value</tt></a>
union.  New nodes can be created using the <a
href="documentation.html#mxmlNewElement"><tt>mxmlNewElement()</tt></a>,
<a
href="documentation.html#mxmlNewInteger"><tt>mxmlNewInteger()</tt></a>,
<a
href="documentation.html#mxmlNewOpaque"><tt>mxmlNewOpaque()</tt></a>,
<a
href="documentation.html#mxmlNewReal"><tt>mxmlNewReal()</tt></a>,
and <a
href="documentation.html#mxmlNewText"><tt>mxmlNewText()</tt></a>
functions. Only elements can have child nodes, and the top node
must be an element, usually "?xml".</p>

<p>Each node has pointers for the node above (<tt>parent</tt>), below (<tt>child</tt>),
to the left (<tt>prev</tt>), and to the right (<tt>next</tt>) of the current
node. If you have an XML file like the following:</p>

<pre>
    &lt;?xml version="1.0"?>
    &lt;data>
        &lt;node>val1&lt;/node>
        &lt;node>val2&lt;/node>
        &lt;node>val3&lt;/node>
        &lt;group>
            &lt;node>val4&lt;/node>
            &lt;node>val5&lt;/node>
            &lt;node>val6&lt;/node>
        &lt;/group>
        &lt;node>val7&lt;/node>
        &lt;node>val8&lt;/node>
        &lt;node>val9&lt;/node>
    &lt;/data>
</pre>

<p>the node tree returned by <tt>mxmlLoadFile()</tt> would look
like the following in memory:</p>

<pre>
    ?xml
      |
    data
      |
    node - node - node - group - node - node - node
      |      |      |      |       |      |      |
    val1   val2   val3     |     val7   val8   val9
                           |
                         node - node - node
                           |      |      |
                         val4   val5   val6
</pre>

<p>where "-" is a pointer to the next node and "|" is a pointer
to the first child node.</p>

<p>Once you are done with the XML data, use the <a
href="documentation.html#mxmlDelete"><tt>mxmlDelete()</tt></a>
function to recursively free the memory that is used for a
particular node or the entire tree:</p>

<pre>
mxmlDelete(tree);
</pre>

<h3>Loading and Saving XML Files</h3>

<p>You load an XML file using the <a
href="documentation.html#mxmlLoadFile"><tt>mxmlLoadFile()</tt></a>
function:</p>

<pre>
FILE *fp;
mxml_node_t *tree;

fp = fopen("filename.xml", "r");
tree = mxmlLoadFile(NULL, fp, MXML_NO_CALLBACK);
fclose(fp);
</pre>

<p>The third argument specifies a callback function which
returns the value type of the immediate children for a new
element node: <tt>MXML_INTEGER</tt>, <tt>MXML_OPAQUE</tt>,
<tt>MXML_REAL</tt>, or <tt>MXML_TEXT</tt>. This function is
called <i>after</i> the element and its attributes have been
read, so you can look at the element name, attributes, and
attribute values to determine the proper value type to return.
The default value type is MXML_TEXT if no callback is used.</p>

<p>Similarly, you save an XML file using the <a
href="documentation.html#mxmlSaveFile"><tt>mxmlSaveFile()</tt></a>
function:</p>

<pre>
FILE *fp;
mxml_node_t *tree;

fp = fopen("filename.xml", "w");
mxmlSaveFile(tree, fp, MXML_NO_CALLBACK);
fclose(fp);
</pre>

<p>Callback functions for saving are used to optionally insert
whitespace before and after elements in the node tree. Your
function will be called up to four times for each element node
with a pointer to the node and a "where" value of
<tt>MXML_WS_BEFORE_OPEN</tt>, <tt>MXML_WS_AFTER_OPEN</tt>,
<tt>MXML_WS_BEFORE_CLOSE</tt>, or <tt>MXML_WS_AFTER_CLOSE</tt>.
The callback function should return 0 if no whitespace should be
added and the character to insert (space, tab, newline)
otherwise.</p>

<p>The <a
href="documentation.html#mxmlLoadString"><tt>mxmlLoadString()</tt></a>,
<a
href="documentation.html#mxmlSaveAllocString"><tt>mxmlSaveAllocString()</tt></a>,
and <a
href="documentation.html#mxmlSaveString"><tt>mxmlSaveString()</tt></a>
functions load XML node trees from and save XML node trees to
strings:</p>

<pre>
char buffer[8192];
char *ptr;
mxml_node_t *tree;

...
tree = mxmlLoadString(NULL, buffer, MXML_NO_CALLBACK);

...
mxmlSaveString(tree, buffer, sizeof(buffer), MXML_NO_CALLBACK);

...
ptr = mxmlSaveAllocString(tree, MXML_NO_CALLBACK);
</pre>

<h3>Finding and Iterating Nodes</h3>

<p>The <a
href="documentation.html#mxmlWalkPrev"><tt>mxmlWalkPrev()</tt></a>
and <a
href="documentation.html#mxmlWalkNext"><tt>mxmlWalkNext()</tt></a>functions
can be used to iterate through the XML node tree:</p>

<pre>
mxml_node_t *node = mxmlWalkPrev(current, tree, MXML_DESCEND);

mxml_node_t *node = mxmlWalkNext(current, tree, MXML_DESCEND);
</pre>

<p>In addition, you can find a named element/node using the <a
href="documentation.html#mxmlFindElement"><tt>mxmlFindElement()</tt></a>
function:</p>

<pre>
mxml_node_t *node = mxmlFindElement(tree, tree, "name", "attr",
                                    "value", MXML_DESCEND);
</pre>

<p>The <tt>name</tt>, <tt>attr</tt>, and <tt>value</tt>
arguments can be passed as <tt>NULL</tt> to act as wildcards,
e.g.:</p>

<pre>
/* Find the first "a" element */
node = mxmlFindElement(tree, tree, "a", NULL, NULL, MXML_DESCEND);

/* Find the first "a" element with "href" attribute */
node = mxmlFindElement(tree, tree, "a", "href", NULL, MXML_DESCEND);

/* Find the first "a" element with "href" to a URL */
node = mxmlFindElement(tree, tree, "a", "href",
                       "http://www.easysw.com/~mike/mxml/", MXML_DESCEND);

/* Find the first element with a "src" attribute*/
node = mxmlFindElement(tree, tree, NULL, "src", NULL, MXML_DESCEND);

/* Find the first element with a "src" = "foo.jpg" */
node = mxmlFindElement(tree, tree, NULL, "src", "foo.jpg", MXML_DESCEND);
</pre>

<p>You can also iterate with the same function:</p>

<pre>
mxml_node_t *node;

for (node = mxmlFindElement(tree, tree, "name", NULL, NULL, MXML_DESCEND);
     node != NULL;
     node = mxmlFindElement(node, tree, "name", NULL, NULL, MXML_DESCEND))
{
  ... do something ...
}
</pre>

<p>The <tt>MXML_DESCEND</tt> argument can actually be one of three constants:</p>

<ul>

	<li><tt>MXML_NO_DESCEND</tt> means to not to look at any
	child nodes in the element hierarchy, just look at
	siblings at the same level or parent nodes until the top
	node or top-of-tree is reached. The previous node from
	"group" would be the "node" element to the left, while
	the next node from "group" would be the "node" element
	to the right.</li>

	<li><tt>MXML_DESCEND_FIRST</tt> means that it is OK to
	descend to the first child of a node, but not to descend
	further when searching. You'll normally use this when
	iterating through direct children of a parent node, e.g.
	all of the "node" elements under the "?xml" parent node
	in the example above. This mode is only applicable to
	the search function; the walk functions treat this as
	<tt>MXML_DESCEND</tt> since every call is a first
	time.</li>

	<li><tt>MXML_DESCEND</tt> means to keep descending until
	you hit the bottom of the tree.  The previous node from
	"group" would be the "val3" node and the next node would
	be the first node element under "group". If you were to
	walk from the root node "?xml" to the end of the
	tree with <tt>mxmlWalkNext()</tt>, the order would be:

<pre>
    ?xml
    data
    node
    val1
    node
    val2
    node
    val3
    group
    node
    val4
    node
    val5
    node
    val6
    node
    val7
    node
    val8
    node
    val9
</pre>

	<p>If you started at "val9" and walked using
	<tt>mxmlWalkPrev()</tt>, the order would be reversed,
	ending at "?xml".</p></li>

</ul>

<h2>Getting Help and Reporting Problems</h2>

<p>You can email me at "mxml <i>at</i> easysw <i>dot</i> com" to
report problems and/or ask for help.  Just don't expect an
instant response, as I get a <i>lot</i> of email...</p>

<h2>Legal Stuff</h2>

<p>The Mini-XML library is Copyright 2003-2004 by Michael Sweet.</p>

<p>This library is free software; you can redistribute it
and/or modify it under the terms of the GNU Library General
Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any
later version.</p>

<p>This library is distributed in the hope that it will be
useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE.  See the GNU Library General Public License for
more details.</p>

<p>You should have received a copy of the GNU Library General
Public License along with this library; if not, write to the
Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA
02139, USA.</p>

<?php

html_footer();

//
// End of "$Id: index.php,v 1.1 2004/05/17 02:14:55 mike Exp $".
//
?>