<?php

/**
 * Copyright 2017 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * For instructions on how to run the full sample:
 *
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/main/video/README.md
 */

namespace Google\Cloud\Samples\VideoIntelligence;

// [START video_analyze_explicit_content]
use Google\Cloud\VideoIntelligence\V1\AnnotateVideoRequest;
use Google\Cloud\VideoIntelligence\V1\Client\VideoIntelligenceServiceClient;
use Google\Cloud\VideoIntelligence\V1\Feature;
use Google\Cloud\VideoIntelligence\V1\Likelihood;

/**
 * @param string $uri The cloud storage object to analyze (gs://your-bucket-name/your-object-name)
 * @param int $pollingIntervalSeconds
 */
function analyze_explicit_content(string $uri, int $pollingIntervalSeconds = 0)
{
    $video = new VideoIntelligenceServiceClient();

    # Execute a request.
    $features = [Feature::EXPLICIT_CONTENT_DETECTION];
    $request = (new AnnotateVideoRequest())
        ->setInputUri($uri)
        ->setFeatures($features);
    $operation = $video->annotateVideo($request);

    # Wait for the request to complete.
    $operation->pollUntilComplete([
        'pollingIntervalSeconds' => $pollingIntervalSeconds
    ]);

    # Print the result.
    if ($operation->operationSucceeded()) {
        $results = $operation->getResult()->getAnnotationResults()[0];
        $explicitAnnotation = $results->getExplicitAnnotation();
        foreach ($explicitAnnotation->getFrames() as $frame) {
            $time = $frame->getTimeOffset();
            printf('At %ss:' . PHP_EOL, $time->getSeconds() + $time->getNanos() / 1000000000.0);
            printf('  pornography: ' . Likelihood::name($frame->getPornographyLikelihood()) . PHP_EOL);
        }
    } else {
        print_r($operation->getError());
    }
}
// [END video_analyze_explicit_content]

// The following 2 lines are only needed to run the samples
require_once __DIR__ . '/../../testing/sample_helpers.php';
\Google\Cloud\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
