function musdecode(input : string) : string {
    //PARAGRAPH
    var out : string = input.replace(/\[p\]/gi, "<p>")
        .replace(/\[\/p\]/gi, "</p>")
        .replace(/\[b\]/gi, "<b>")
        .replace(/\[\/b\]/gi, "</b>")
        .replace(/\[i\]/gi, "<i>")
        .replace(/\[\/i\]/gi, "</i>")
        .replace(/\[u\]/, "<u>")
        .replace(/\[\/u\]/, "</u>")
        .replace(/\r\n|\r|\n/, "<br>");

    let imgdata = out.match(/\[IMG\((.+?)\)\((.+?)\)\((.+?)\)\]/im);
    if (imgdata !== null) {
        if (imgdata.length >= 4) {
            let str = '<img src="' + imgdata[1] + '" alt="' + imgdata[2] + '" title="' + imgdata[3] + '" class="u_image" />';
            out = out.replace(/\[IMG\((.+?)\)\((.+?)\)\((.+?)\)\]/im, str);
        }
    }

    let anchordata = out.match(/\[a\((.+?)\)\((.+?)\)\](.+?)\[\/a\]/im);
    if (anchordata !== null) {
        if (anchordata.length >= 4) {
            let str = '<a href="' + anchordata[1] + '" class="u_anchor" title="' + anchordata[2] + '" target="_blank" />' + anchordata[3] + '</a>';
            out = out.replace(/\[a\((.+?)\)\((.+?)\)\](.+?)\[\/a\]/im, str);
        }
    }

    return out;
}