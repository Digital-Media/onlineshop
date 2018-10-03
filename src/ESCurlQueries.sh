#!/usr/bin/env bash

curl -XGET "http://localhost:9200"


curl -X POST "localhost:9200/product/_analyze" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Almgrundstueck an einem Bergsee"
}
'
curl -XGET 'http://localhost:9200/product/_count'
curl -XGET "http://localhost:9200/product/_doc/_search?pretty=true" -H 'Content-Type: application/json' -d '
{
    "query" : {
        "match_all" : {}
    }
}
'

curl -X POST "localhost:9200/product_german/_analyze" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Almgrundstueck an einem Bergsee"
}
'

curl -X POST "localhost:9200/product_dict_decompounder/_analyze" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Almgrundstueck an einem Bergsee"
}
'
curl -X GET "localhost:9200/product_hyphen_decompounder/_search" -H 'Content-Type: application/json' -d'
{
  "from" : 0, "size" : 2,
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}}'
curl -X GET "localhost:9200/product_hyphen_decompounder/_search" -H 'Content-Type: application/json' -d'
{
  "from" : 2, "size" : 2,
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}}'
curl -X GET "localhost:9200/product_hyphen_decompounder/_search" -H 'Content-Type: application/json' -d'
{
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}}'
curl -XGET "http://localhost:9200/product_hyphen_decompounder/_doc/_search?pretty=true" -H 'Content-Type: application/json' -d '
{
    "query" : {
        "match_all" : {}
    }
}
'
curl -X POST "localhost:9200/product_hyphen_decompounder/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "product_name",
  "text": "Reihenhäuser"
}
'
curl -X POST "localhost:9200/product_hyphen_decompounder/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Häuser mit Mehrwert"
}
'
curl -X POST "localhost:9200/product_hyphen_decompounder/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "long_description",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'
curl -X POST "localhost:9200/product_hyphen_decompounder/_analyze?pretty=true" -H 'Content-Type: application/json' -d'
{
  "field": "search",
  "text": "Schöne Reihenhäuser im Grünen mit Blick in die Berge"
}
'
