#!/usr/bin/env bash

# Read configuration of Elasticsearch
curl -XGET "http://localhost:9200"

# Providing a Stop Analyzer for an English text
curl -X POST "localhost:9200/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "stop",
  "text": "The 2 QUICK Brown-Foxes jumped over the lazy dog\u0027s bone."
}
'

# Creating an Index with a Stop Analyzer with a stop word list
curl -X PUT "localhost:9200/my_index" -H 'Content-Type: application/json' -d'
{
  "settings": {
    "analysis": {
      "analyzer": {
        "my_stop_analyzer": {
          "type": "stop",
          "stopwords": ["the", "over"]
        }
      }
    }
  }
}
'
# Using the index to analyze a test text
curl -X POST "localhost:9200/my_index/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "my_stop_analyzer",
  "text": "The 2 QUICK Brown-Foxes jumped over the lazy dog\u0027s bone."
}
'

# Deleting the index
curl -X DELETE "localhost:9200/my_index"

# Examples for the index product created with ESCreateIndex.sh

# Counting the entries
curl -XGET 'http://localhost:9200/product/_count?pretty=true'

# Displaying all entries
curl -XGET "http://localhost:9200/product/_doc/_search?pretty=true" -H 'Content-Type: application/json' -d '
{
    "query" : {
        "match_all" : {}
    }
}
'

# Matching search terms in the copy_to field search
curl -X GET "localhost:9200/product/_search" -H 'Content-Type: application/json' -d'
{
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}
'

# Matching search terms in product_name with standard analyzer
curl -X GET "localhost:9200/product/_search" -H 'Content-Type: application/json' -d'
{
    "query": {
        "match" : {
            "product_name" : {
                "query" : "haus alm"
            }
        }
    }
}
'

# Matching search terms in product_name with standard analyzer
curl -X GET "localhost:9200/product/_search" -H 'Content-Type: application/json' -d'
{
    "query": {
        "match" : {
            "product_name" : {
                "query" : "passivhaus"
            }
        }
    }
}
'

# Using pagination for the matching results
curl -X GET "localhost:9200/product/_search" -H 'Content-Type: application/json' -d'
{
  "from" : 0, "size" : 2,
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}
'

curl -X GET "localhost:9200/product/_search" -H 'Content-Type: application/json' -d'
{
  "from" : 2, "size" : 2,
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}
'

# Testing different analyzers of the index

# If using only a text field ES uses the standard analyzer without custom filtering
curl -X POST "localhost:9200/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Providing an analyzer forces ES to use the given one. In this case the standard analyzer.
# That gives the same result as above
curl -X POST "localhost:9200/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "standard",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Giving a field name forces ES to use the analyzer mapped to the field during index creation for the given text
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "product_name",
  "text": "Reihenhäuser"
}
'

curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Häuser mit Mehrwert"
}
'

curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "long_description",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "search",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Using the analyzer german defined during index creation for a test text.
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Using the filter german stemmer defined during index creation for a test text.
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_stemmer",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge erreichbar über eine Straße"
}
'

# Using the analyzer german defined during index creation for a test text.
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_normalization",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge erreichbar über eine Straße"
}
'

# Using the analyzer german_dict_decompound, that uses a german word decompounder against a dictionary (brute force)
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_dict_decompound",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge erreichbar über eine Straße"
}
'

# compare the hyphenation decompounder with the dictionary decompounder
# matches grund, because short_description has been asigned the hyphenation decompounder
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Almgrundstueck an einem Bergsee"
}
'

# matches grund and rund!! but it is obvious, that rund ist not part of Almgrundstueck
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_dict_decompound",
  "text": "Almgrundstueck an einem Bergsee"
}
'

# matches grund and rund!! but it is obvious, that rund ist not part of Almgrundstueck
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_decompound",
  "text": "Almgrundstueck an einem Bergsee"
}
'

# Using the analyzer german_dict_decompound, that uses a intelligent algorithm to decompound words
# before they are compared to a german dictionary
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_decompound",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Using an filter for stop words
curl -X POST "localhost:9200/product/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "analyzer": "german_stop",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'

# Index with multi fields
curl -X PUT "localhost:9200/my_index" -H 'Content-Type: application/json' -d'
{
  "mappings": {
      "properties": {
        "text": {
          "type": "text",
          "fields": {
            "german": {
              "type":     "text",
              "analyzer": "german"
            }
          }
        }
      }
  }
}
'

# Adding two entries
curl -X PUT "localhost:9200/my_index/_doc/1" -H 'Content-Type: application/json' -d'
  { "text": "Schöne neue Häuser" }
'

curl -X PUT "localhost:9200/my_index/_doc/2" -H 'Content-Type: application/json' -d'
  { "text": "Schönes neues Haus" }
'

# standard analyzer
curl -X POST "localhost:9200/my_index/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
  { "text": "Schöne neue Häuser" }
'

curl -X POST "localhost:9200/my_index/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
  { "text": "Schönes neues Haus" }
'

# german analyzer
curl -X POST "localhost:9200/my_index/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
  {
    "analyzer" : "german" ,
    "text": "Schöne neue Häuser"
  }
'

curl -X POST "localhost:9200/my_index/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
  {
    "analyzer" : "german" ,
    "text": "Schönes neues Haus"
  }
'

# Relevance is different for these two entries in the following search queries. Sort is different in results
curl -X GET "localhost:9200/my_index/_search?pretty=true" -H 'Content-Type: application/json' -d'
{
  "query": {
    "multi_match": {
      "query": "Schönes neues Haus",
      "fields": [
        "text",
        "text.german"
      ],
      "type": "most_fields"
    }
  }
}
'

curl -X GET "localhost:9200/my_index/_search?pretty=true" -H 'Content-Type: application/json' -d'
{
  "query": {
    "multi_match": {
      "query": "Schöne neue Häuser",
      "fields": [
        "text",
        "text.german"
      ],
      "type": "most_fields"
    }
  }
}
'

# Explain different relevance for these two entries
curl -X GET "localhost:9200/my_index/_search?pretty=true" -H 'Content-Type: application/json' -d'
{
  "query": {
    "multi_match": {
      "query": "Schönes neues Haus",
      "fields": [
        "text",
        "text.german"
      ],
      "type": "most_fields"
    }
  },
  "explain": true
}
'

curl -X GET "localhost:9200/my_index/_search?pretty=true" -H 'Content-Type: application/json' -d'
{
  "query": {
    "multi_match": {
      "query": "Schöne neue Häuser",
      "fields": [
        "text",
        "text.german"
      ],
      "type": "most_fields"
    }
  },
  "explain": true
}
'

# Index settings
curl -XGET "http://localhost:9200/my_index/_settings?pretty=true"

# Deleting the index
curl -X DELETE "localhost:9200/my_index"
