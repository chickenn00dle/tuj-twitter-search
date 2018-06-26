# TUJ Twitter Search for WordPress

## Description

TUJ Twitter Search for WordPress replaces the depracated twitter embedded search functionality for the TUJ in the Media section of our news pages:

* [www.tuj.ac.jp/news/](https://www.tuj.ac.jp/news/)
* [www.tuj.ac.jp/jp/news/](https://www.tuj.ac.jp/jp/news/)

The widget plugin uses the standard [Twitter Search API](https://developer.twitter.com/en/docs/tweets/search/api-reference/get-search-tweets) to grab tweets from the @tujweb twitter account with the specified hashtags.

## Installation

1. Clone the GitHub repo into the wp-content/plugins folder

```
$ git clone https://github.com/TUJapan/tujinthemedia-plugin.git
```
2. From the wp-admin screen, activate the `TUJ Twitter Search Widget`
3. The `TUJTS` admin menu and `Twitter Search Widget` should now be available

## Usage

Before adding a Twitter Search widget, be sure to add your Twitter OAuth and Consumer Keys/Tokens to the TUJTS Admin menu!

The current version of the widget has four input options:

**Title**: The title which will display at the top of each widget. In our case, 'TUJ in the Media', Faculty Quotes, Publications.

**Title Class**: The CSS class for the above title.

**Hashtag**: The Hashtag you would like the widget instance to perform the search on. In our case, #TUJCampus, #TUJFaculty, #TUJPublication. (The search will only go 7 days back!!!)

**Banner Source**: An optional url to use as the banner on. (A good size to go with is 700x233px. Breakpoint will make widget space take up to 680px on smaller screens.)

## Author

* **Rasmy Nguyen** - rasmy.nguyen@tuj.temple.edu

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT)

## Acknowledgments

* Shout out to [ValentinH](https://gist.github.com/ValentinH) Whose [queryTwitter.php](https://gist.github.com/ValentinH/8266635) template saved me tons of time figuring out complicated Twitter oauth things.