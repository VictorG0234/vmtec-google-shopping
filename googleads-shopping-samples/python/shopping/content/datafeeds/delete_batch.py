#!/usr/bin/python
#
# Copyright 2016 Google Inc. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
"""Deletes several datafeeds from the specified account, in a single batch."""

from __future__ import print_function
import argparse
import json
import sys

from shopping.content import common

# Declare command-line flags.
argparser = argparse.ArgumentParser(add_help=False)
argparser.add_argument(
    'datafeed_ids', nargs='+', help='The IDs of the datafeeds to delete.')


def main(argv):
  # Authenticate and construct service.
  service, config, flags = common.init(
      argv, __doc__, parents=[argparser])
  merchant_id = config['merchantId']
  datafeed_ids = flags.datafeed_ids

  batch = {
      'entries': [{
          'batchId': i,
          'merchantId': merchant_id,
          'method': 'delete',
          'datafeedId': v,
      } for i, v in enumerate(datafeed_ids)],
  }

  request = service.datafeeds().custombatch(body=batch)
  result = request.execute()

  if result['kind'] == 'content#datafeedsCustomBatchResponse':
    for entry in result['entries']:
      errors = entry.get('errors')
      if errors:
        print('Errors for batch entry %d:' % entry['batchId'])
        print(json.dumps(errors, sort_keys=True, indent=2,
                         separators=(',', ': ')))
      else:
        print('Successfully deleted datafeed %s (batch entry %d).' %
              (datafeed_ids[entry['batchId']], entry['batchId']))
  else:
    print('There was an error. Response: %s' % result)


if __name__ == '__main__':
  main(sys.argv)
