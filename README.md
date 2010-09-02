NSM File - Beta file fieldtype for EE2
========

* Adds credit, caption and subject textareas to file uploads.
* Adds size and style drop downs for css class hooks.
* Drop in replacement for the default file fieldtype

Install
-------

1. Download repo
2. Rename folder to nsm_file
3. Move to expressionengine/third_party
4. Move themes/third_party/nsm_file to themes/third_party

Tag
---

Use as a single tag or tag pair.

Single tag outputs the full url

{my_custom_field} => http://mysite.com/uploads/myfile.jpg

Tag pair has the following nested tags

	{my_custom_field}
		[caption] => {caption}
		[credit] => {credit}
		[subject] => {subject}
		[style] => {style}
		[size] => {size}
		[filedir] => {filedir}
		[filename] => {filename}
		[is_image] => {is_image}
		[extension] => {extension}
		[path] => {path}
		[server_path] => {server_path}
		[thumb] => {thumb}
	{/my_custom_field}